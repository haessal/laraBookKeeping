<?php

namespace Tests\Feature\api\v1\Slips;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Book;
use App\Models\Permission;
use App\Models\Slip;
use App\Models\SlipEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DefaultBookSlipRetrievalTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\User */
    private $otherUser;

    /** @var \App\Models\Book */
    private $book;

    /** @var \App\Models\Book */
    private $unavailableBook;

    /** @var \App\Models\AccountGroup */
    private $accountGroup;

    /** @var \App\Models\Account */
    private $debit;

    /** @var \App\Models\Account */
    private $credit;

    /** @var \App\Models\Slip */
    private $slip;

    /** @var \App\Models\SlipEntry */
    private $slipEntry;

    /** @var \App\Models\Slip */
    private $unavailableSlip;

    public function setup(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->book = Book::factory()->create([
            'book_name' => $this->faker->word(),
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->user->id,
            'readable_book'  => $this->book->book_id,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => true,
        ]);
        $this->unavailableBook = Book::factory()->create([
            'book_name' => $this->faker->word(),
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->otherUser->id,
            'readable_book'  => $this->unavailableBook->book_id,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => true,
        ]);
        $this->accountGroup = AccountGroup::factory()->create([
            'book_id'      => $this->book->book_id,
            'account_type' => 'asset',
            'is_current'   => true,
        ]);
        $this->debit = Account::factory()->create([
            'account_group_id' => $this->accountGroup->account_group_id,
            'selectable'       => true,
        ]);
        $this->credit = Account::factory()->create([
            'account_group_id' => $this->accountGroup->account_group_id,
            'selectable'       => true,
        ]);
        $this->slip = Slip::factory()->create([
            'book_id'  => $this->book->book_id,
            'is_draft' => false,
        ]);
        $this->slipEntry = SlipEntry::factory()->create([
            'slip_id' => $this->slip->slip_id,
            'debit'   => $this->debit->account_id,
            'credit'  => $this->credit->account_id,
        ]);
        $this->unavailableSlip = Slip::factory()->create([
            'book_id'  => $this->unavailableBook->book_id,
            'is_draft' => false,
        ]);
    }

    public function test_specified_slip_of_default_book_can_be_retrieved(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/api/v1/slips/'.$this->slip->slip_id);

        $response->assertOk()
            ->assertJson([
                'id'      => $this->slip->slip_id,
                'date'    => $this->slip->date,
                'outline' => $this->slip->slip_outline,
                'memo'    => $this->slip->slip_memo,
                'entries' => [
                    [
                        'id'      => $this->slipEntry->slip_entry_id,
                        'debit'   => [
                            'id'    => $this->debit->account_id,
                            'title' => $this->debit->account_title,
                        ],
                        'credit'  => [
                            'id'    => $this->credit->account_id,
                            'title' => $this->credit->account_title,
                        ],
                        'amount'  => $this->slipEntry->amount,
                        'client'  => $this->slipEntry->client,
                        'outline' => $this->slipEntry->outline,
                    ],
                ],
            ]);
    }

    public function test_slip_is_not_retrieved_with_invalid_path_parameter_for_slip_id(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/api/v1/slips/0');

        $response->assertBadRequest();
    }

    public function test_default_book_is_not_found(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->get('/api/v1/slips/'.$this->slip->slip_id);

        $response->assertNotFound();
    }

    public function test_specified_slip_is_not_found(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/api/v1/slips/'.$this->unavailableSlip->slip_id);

        $response->assertNotFound();
    }
}
