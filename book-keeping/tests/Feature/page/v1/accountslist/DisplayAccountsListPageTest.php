<?php

namespace Tests\Feature\page\v1\accountslist;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Book;
use App\Models\Permission;
use App\Models\User;
use App\Service\AccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DisplayAccountsListPageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\User */
    private $userWhoDoesNotHaveBook;

    /** @var \App\Models\Book */
    private $book;

    /** @var \App\Models\AccountGroup */
    private $accountGroup_1100;

    /** @var \App\Models\AccountGroup */
    private $accountGroup_0;

    /** @var \App\Models\Account */
    private $account_1101;

    /** @var \App\Models\Account */
    private $account_0;

    public function setup(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->book = Book::factory()->create([
            'book_name' => $this->faker->word(),
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->user->id,
            'readable_book' => $this->book->book_id,
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => true,
        ]);
        $this->accountGroup_1100 = AccountGroup::factory()->create([
            'book_id' => $this->book->book_id,
            'account_type' => AccountService::ACCOUNT_TYPE_ASSET,
            'is_current' => true,
            'account_group_bk_code' => 1100,
        ]);
        $this->accountGroup_0 = AccountGroup::factory()->create([
            'book_id' => $this->book->book_id,
            'account_type' => AccountService::ACCOUNT_TYPE_ASSET,
            'is_current' => true,
            'account_group_bk_code' => 0,
        ]);
        $this->account_1101 = Account::factory()->create([
            'account_group_id' => $this->accountGroup_1100->account_group_id,
            'selectable' => true,
            'account_bk_code' => 1101,
        ]);
        $this->account_0 = Account::factory()->create([
            'account_group_id' => $this->accountGroup_0->account_group_id,
            'selectable' => true,
            'account_bk_code' => 0,
        ]);
        $this->userWhoDoesNotHaveBook = User::factory()->create();
    }

    public function test_accountslist_page_can_be_diplayed(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/page/v1/accountslist');

        $response->assertOk();
    }

    public function test_accountslist_page_does_not_display_because_default_book_is_not_found(): void
    {
        $response = $this->actingAs($this->userWhoDoesNotHaveBook)
            ->get('/page/v1/accountslist');

        $response->assertNotFound();
    }
}
