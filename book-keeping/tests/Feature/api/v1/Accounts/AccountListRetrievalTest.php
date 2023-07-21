<?php

namespace Tests\Feature\api\v1\Accounts;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Book;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountListRetrievalTest extends TestCase
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
    private $account;

    public function setup(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->book = Book::factory()->create([
            'book_name' => $this->faker->word,
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->user->id,
            'readable_book'  => $this->book->book_id,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => false,
        ]);
        $this->unavailableBook = Book::factory()->create([
            'book_name' => $this->faker->word,
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->otherUser->id,
            'readable_book'  => $this->unavailableBook->book_id,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => true,
        ]);
        $this->accountGroup = AccountGroup::factory()->create([
            'book_id' => $this->book->book_id,
            'account_type' => 'asset',
            'is_current' => true,
        ]);
        $this->account = Account::factory()->create([
            'account_group_id' => $this->accountGroup->account_group_id,
            'selectable' => true,
        ]);
    }

    public function test_account_of_specified_book_can_be_retrieved(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/api/v1/books/'.$this->book->book_id.'/accounts');

        $response->assertOk()
            ->assertJsonFragment([
                [
                    'id'          => $this->account->account_id,
                    'title'       => $this->account->account_title,
                    'description' => $this->account->description,
                    'group'       => $this->accountGroup->account_group_id,
                    'group_title' => $this->accountGroup->account_group_title,
                    'is_current'  => true,
                    'type'        => 'asset',
                ],
            ]);
    }

    public function test_account_is_not_retrieved_with_invalid_path_parameter_for_book_id(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/api/v1/books/0/accounts');

        $response->assertBadRequest();
    }

    public function test_specified_book_is_not_found(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/api/v1/books/'.$this->unavailableBook->book_id.'/accounts');

        $response->assertNotFound();
    }
}
