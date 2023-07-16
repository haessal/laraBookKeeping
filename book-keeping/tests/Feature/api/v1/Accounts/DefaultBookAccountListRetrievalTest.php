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

class DefaultBookAccountListRetrievalTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\User */
    private $otherUser;

    /** @var \App\Models\Book */
    private $book;

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

    public function test_account_of_default_book_can_be_retrieved(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/api/v1/accounts');

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

    public function test_default_book_is_not_found(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->get('/api/v1/accounts');

        $response->assertNotFound();
    }
}
