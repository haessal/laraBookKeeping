<?php

namespace Tests\Feature\api\v1\Slips;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Book;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class SlipCreationTest extends TestCase
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
            'readable_book' => $this->book->book_id,
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => false,
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->otherUser->id,
            'readable_book' => $this->book->book_id,
            'modifiable' => false,
            'is_owner' => false,
            'is_default' => false,
        ]);
        $this->unavailableBook = Book::factory()->create([
            'book_name' => $this->faker->word(),
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->otherUser->id,
            'readable_book' => $this->unavailableBook->book_id,
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => true,
        ]);
        $this->accountGroup = AccountGroup::factory()->create([
            'book_id' => $this->book->book_id,
            'account_type' => 'asset',
            'is_current' => true,
        ]);
        $this->debit = Account::factory()->create([
            'account_group_id' => $this->accountGroup->account_group_id,
            'selectable' => true,
        ]);
        $this->credit = Account::factory()->create([
            'account_group_id' => $this->accountGroup->account_group_id,
            'selectable' => true,
        ]);
    }

    public function test_slip_of_specified_book_can_be_created(): void
    {
        $slipDate = $this->faker->date();
        $slipOutline = $this->faker->sentence();
        $slipMemo = $this->faker->sentence();
        $amount = $this->faker->numberBetween(1);
        $client = $this->faker->word();
        $outline = $this->faker->sentence();

        $response = $this->actingAs($this->user)
            ->post('/api/v1/books/'.$this->book->book_id.'/slips', [
                'date' => $slipDate,
                'outline' => $slipOutline,
                'memo' => $slipMemo,
                'entries' => [
                    [
                        'debit' => $this->debit->account_id,
                        'credit' => $this->credit->account_id,
                        'amount' => $amount,
                        'client' => $client,
                        'outline' => $outline,
                    ],
                ],
            ]);

        $response->assertCreated()
            ->assertJsonStructure(['id', 'date', 'outline', 'memo', 'entries'])
            ->assertJson([
                'date' => $slipDate,
                'outline' => $slipOutline,
                'memo' => $slipMemo,
            ])
            ->assertJsonFragment([
                'debit' => [
                    'id' => $this->debit->account_id,
                    'title' => $this->debit->account_title,
                ],
            ])
            ->assertJsonFragment([
                'credit' => [
                    'id' => $this->credit->account_id,
                    'title' => $this->credit->account_title,
                ],
            ])
            ->assertJsonFragment([
                'amount' => $amount,
                'client' => $client,
                'outline' => $outline,
            ]);
        $this->assertDatabaseHas('bk2_0_slips', [
            'slip_id' => $response['id'],
            'book_id' => $this->book->book_id,
            'slip_outline' => $slipOutline,
            'slip_memo' => $slipMemo,
            'date' => $slipDate,
            'is_draft' => false,
        ]);
        $this->assertDatabaseHas('bk2_0_slip_entries', [
            'slip_entry_id' => $response['entries'][0]['id'],
            'slip_id' => $response['id'],
            'debit' => $this->debit->account_id,
            'credit' => $this->credit->account_id,
            'amount' => $amount,
            'client' => $client,
            'outline' => $outline,
        ]);
    }

    public function test_slip_is_not_created_with_invalid_path_parameter_for_book_id(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/api/v1/books/0/slips', [
                'memo' => 0,
            ]);

        $response->assertBadRequest();
    }

    public function test_slip_is_not_created_with_invalid_request_body_1(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/api/v1/books/'.$this->book->book_id.'/slips', [
                'memo' => 0,
            ]);

        $response->assertBadRequest();
    }

    public function test_slip_is_not_created_with_invalid_request_body_2(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/api/v1/books/'.$this->book->book_id.'/slips', [
                'date' => 'date',
                'memo' => '',
                'entries' => 'entries',
            ]);

        $response->assertBadRequest();
    }

    public function test_slip_is_not_created_with_invalid_request_body_3(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/api/v1/books/'.$this->book->book_id.'/slips', [
                'entries' => ['entry'],
            ]);

        $response->assertBadRequest();
    }

    public function test_slip_is_not_created_with_invalid_request_body_4(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/api/v1/books/'.$this->book->book_id.'/slips', [
                'entries' => [
                    [
                        'debit' => 'debit',
                        'credit' => 'credit',
                        'amount' => 0,
                        'client' => '',
                        'outline' => '',
                    ],
                ],
            ]);

        $response->assertBadRequest();
    }

    public function test_slip_is_not_created_with_invalid_request_body_5(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/api/v1/books/'.$this->book->book_id.'/slips', [
                'entries' => [
                    [
                        'debit' => $this->debit->account_id,
                        'credit' => $this->debit->account_id,
                    ],
                ],
            ]);

        $response->assertBadRequest();
    }

    public function test_specified_book_is_not_found(): void
    {
        $slipDate = $this->faker->date();
        $slipOutline = $this->faker->sentence();
        $slipMemo = $this->faker->sentence();
        $amount = $this->faker->numberBetween(1);
        $client = $this->faker->word();
        $outline = $this->faker->sentence();

        $response = $this->actingAs($this->user)
            ->post('/api/v1/books/'.$this->unavailableBook->book_id.'/slips', [
                'date' => $slipDate,
                'outline' => $slipOutline,
                'memo' => $slipMemo,
                'entries' => [
                    [
                        'debit' => $this->debit->account_id,
                        'credit' => $this->credit->account_id,
                        'amount' => $amount,
                        'client' => $client,
                        'outline' => $outline,
                    ],
                ],
            ]);

        $response->assertNotFound();
    }

    public function test_slip_is_not_created_without_permission(): void
    {
        $slipDate = $this->faker->date();
        $slipOutline = $this->faker->sentence();
        $slipMemo = $this->faker->sentence();
        $amount = $this->faker->numberBetween(1);
        $client = $this->faker->word();
        $outline = $this->faker->sentence();

        $response = $this->actingAs($this->otherUser)
            ->post('/api/v1/books/'.$this->book->book_id.'/slips', [
                'date' => $slipDate,
                'outline' => $slipOutline,
                'memo' => $slipMemo,
                'entries' => [
                    [
                        'debit' => $this->debit->account_id,
                        'credit' => $this->credit->account_id,
                        'amount' => $amount,
                        'client' => $client,
                        'outline' => $outline,
                    ],
                ],
            ]);

        $response->assertForbidden();
    }

    public function test_slip_is_not_created_with_invalid_account(): void
    {
        $slipDate = $this->faker->date();
        $slipOutline = $this->faker->sentence();
        $slipMemo = $this->faker->sentence();
        $amount = $this->faker->numberBetween(1);
        $client = $this->faker->word();
        $outline = $this->faker->sentence();

        $response = $this->actingAs($this->user)
            ->post('/api/v1/books/'.$this->book->book_id.'/slips', [
                'date' => $slipDate,
                'outline' => $slipOutline,
                'memo' => $slipMemo,
                'entries' => [
                    [
                        'debit' => $this->debit->account_id,
                        'credit' => (string) Str::uuid(),
                        'amount' => $amount,
                        'client' => $client,
                        'outline' => $outline,
                    ],
                ],
            ]);

        $response->assertUnprocessable();
    }
}
