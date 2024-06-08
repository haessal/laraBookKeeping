<?php

namespace Tests\Feature\page\v2\books\bookId\accounts;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Book;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SelectAccountsSettingTargetTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Models\User */
    private $user;

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
        $this->accountGroup = AccountGroup::factory()->create([
            'book_id' => $this->book->book_id,
        ]);
        $this->account = Account::factory()->create([
            'account_group_id' => $this->accountGroup->account_group_id,
        ]);
    }

    public function test_selecting_account_group_correctly_redirected(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v2/books/'.$this->book->book_id.'/accounts/settings', [
                'accountsgroup' => $this->accountGroup->account_group_id,
            ]);

        $response->assertRedirectToRoute('v2_accounts_groups', [
            $this->book->book_id,
            $this->accountGroup->account_group_id,
        ]);
    }

    public function test_selecting_account_item_correctly_redirected(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v2/books/'.$this->book->book_id.'/accounts/settings', [
                'accountsitem' => $this->account->account_id,
            ]);

        $response->assertRedirectToRoute('v2_accounts_items', [
            $this->book->book_id,
            $this->account->account_id,
        ]);
    }
}
