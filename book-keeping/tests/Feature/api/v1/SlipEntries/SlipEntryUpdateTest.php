<?php

namespace Tests\Feature\api\v1\SlipEntries;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Book;
use App\Models\Permission;
use App\Models\Slip;
use App\Models\SlipEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class SlipEntryUpdateTest extends TestCase
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

    /** @var \App\Models\SlipEntry */
    private $unavailableSlipEntry;

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
            'is_default'     => false,
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->otherUser->id,
            'readable_book'  => $this->book->book_id,
            'modifiable'     => false,
            'is_owner'       => false,
            'is_default'     => false,
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
        $this->unavailableSlipEntry = SlipEntry::factory()->create([
            'slip_id' => $this->unavailableSlip->slip_id,
            'debit'   => $this->debit->account_id,
            'credit'  => $this->credit->account_id,
        ]);
    }

    public function test_specified_slip_entry_of_specified_book_can_be_updated(): void
    {
        $newAmount = $this->faker->numberBetween(1);
        $newClient = $this->faker->word();
        $newOutline = $this->faker->sentence();

        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/'.$this->book->book_id.'/slipentries/'.$this->slipEntry->slip_entry_id, [
                'debit'    => $this->credit->account_id,
                'credit'   => $this->debit->account_id,
                'amount'   => $newAmount,
                'client'   => $newClient,
                'outline'  => $newOutline,
            ]);

        $response->assertOk()
            ->assertJsonFragment([
                [
                    'id'      => $this->slipEntry->slip_entry_id,
                    'debit'   => [
                        'id'    => $this->credit->account_id,
                        'title' => $this->credit->account_title,
                    ],
                    'credit'  => [
                        'id'    => $this->debit->account_id,
                        'title' => $this->debit->account_title,
                    ],
                    'amount'  => $newAmount,
                    'client'  => $newClient,
                    'outline' => $newOutline,
                    'slip' => [
                        'id'      => $this->slip->slip_id,
                        'date'    => $this->slip->date,
                        'outline' => $this->slip->slip_outline,
                        'memo'    => $this->slip->slip_memo,
                    ],
                ],
            ]);
        $this->assertDatabaseHas('bk2_0_slip_entries', [
            'slip_entry_id' => $this->slipEntry->slip_entry_id,
            'slip_id'       => $this->slip->slip_id,
            'debit'         => $this->credit->account_id,
            'credit'        => $this->debit->account_id,
            'amount'        => $newAmount,
            'client'        => $newClient,
            'outline'       => $newOutline,
        ]);
    }

    public function test_slip_entry_is_not_updated_with_invalid_path_parameter_for_book_id(): void
    {
        $newAmount = $this->faker->numberBetween(1);
        $newClient = $this->faker->word();
        $newOutline = $this->faker->sentence();

        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/0/slipentries/'.$this->slipEntry->slip_entry_id, [
                'debit'   => $this->credit->account_id,
                'credit'  => $this->debit->account_id,
                'amount'  => $newAmount,
                'client'  => $newClient,
                'outline' => $newOutline,
            ]);

        $response->assertBadRequest();
    }

    public function test_slip_entry_is_not_updated_with_invalid_path_parameter_for_slip_entry_id(): void
    {
        $newAmount = $this->faker->numberBetween(1);
        $newClient = $this->faker->word();
        $newOutline = $this->faker->sentence();

        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/'.$this->book->book_id.'/slipentries/0', [
                'debit'   => $this->credit->account_id,
                'credit'  => $this->debit->account_id,
                'amount'  => $newAmount,
                'client'  => $newClient,
                'outline' => $newOutline,
            ]);

        $response->assertBadRequest();
    }

    public function test_slip_entry_is_not_updated_with_invalid_request_body_1(): void
    {
        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/'.$this->book->book_id.'/slipentries/'.$this->slipEntry->slip_entry_id, [
                'unknown' => 'unknown',
            ]);

        $response->assertBadRequest();
    }

    public function test_slip_entry_is_not_updated_with_invalid_request_body_2(): void
    {
        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/'.$this->book->book_id.'/slipentries/'.$this->slipEntry->slip_entry_id, [
                'debit'   => 'debit',
                'credit'  => 'debit',
                'amount'  => 0,
                'client'  => '',
                'outline' => '',
            ]);

        $response->assertBadRequest();
    }

    public function test_slip_entry_is_not_updated_with_invalid_request_body_3(): void
    {
        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/'.$this->book->book_id.'/slipentries/'.$this->slipEntry->slip_entry_id, [
                'debit' => 'debit',
            ]);

        $response->assertBadRequest();
    }

    public function test_slip_entry_is_not_updated_with_invalid_request_body_4(): void
    {
        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/'.$this->book->book_id.'/slipentries/'.$this->slipEntry->slip_entry_id, [
                'credit' => 'credit',
            ]);

        $response->assertBadRequest();
    }

    public function test_specified_book_is_not_found(): void
    {
        $newAmount = $this->faker->numberBetween(1);
        $newClient = $this->faker->word();
        $newOutline = $this->faker->sentence();

        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/'.$this->unavailableBook->book_id.'/slipentries/'.$this->slipEntry->slip_entry_id, [
                'debit'   => $this->credit->account_id,
                'credit'  => $this->debit->account_id,
                'amount'  => $newAmount,
                'client'  => $newClient,
                'outline' => $newOutline,
            ]);

        $response->assertNotFound();
    }

    public function test_specified_slip_entry_is_not_found(): void
    {
        $newAmount = $this->faker->numberBetween(1);
        $newClient = $this->faker->word();
        $newOutline = $this->faker->sentence();

        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/'.$this->book->book_id.'/slipentries/'.$this->unavailableSlipEntry->slip_entry_id, [
                'debit'   => $this->credit->account_id,
                'credit'  => $this->debit->account_id,
                'amount'  => $newAmount,
                'client'  => $newClient,
                'outline' => $newOutline,
            ]);

        $response->assertNotFound();
    }

    public function test_slip_entry_is_not_updated_without_permission(): void
    {
        $newAmount = $this->faker->numberBetween(1);
        $newClient = $this->faker->word();
        $newOutline = $this->faker->sentence();

        $response = $this->actingAs($this->otherUser)
            ->patch('/api/v1/books/'.$this->book->book_id.'/slipentries/'.$this->slipEntry->slip_entry_id, [
                'debit'   => $this->credit->account_id,
                'credit'  => $this->debit->account_id,
                'amount'  => $newAmount,
                'client'  => $newClient,
                'outline' => $newOutline,
            ]);

        $response->assertForbidden();
    }

    public function test_slip_entry_is_not_updated_with_invalid_account(): void
    {
        $newAmount = $this->faker->numberBetween(1);
        $newClient = $this->faker->word();
        $newOutline = $this->faker->sentence();

        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/'.$this->book->book_id.'/slipentries/'.$this->slipEntry->slip_entry_id, [
                'debit'    => $this->debit->account_id,
                'credit'   => (string) Str::uuid(),
                'amount'   => $newAmount,
                'client'   => $newClient,
                'outline'  => $newOutline,
            ]);

        $response->assertUnprocessable();
    }
}
