<?php

namespace Tests\Feature\page\v2\books\bookId\accounts;

use App\Models\AccountGroup;
use App\Models\Book;
use App\Models\Permission;
use App\Models\User;
use App\Service\AccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateAccountsGroupTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\User */
    private $userWhoDoesNotHaveWritePermission;

    /** @var \App\Models\User */
    private $userWhoDoesNotHaveBook;

    /** @var \App\Models\Book */
    private $book;

    /** @var \App\Models\AccountGroup */
    private $accountGroup;

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
        $this->userWhoDoesNotHaveWritePermission = User::factory()->create();
        Permission::factory()->create([
            'permitted_user' => $this->userWhoDoesNotHaveWritePermission->id,
            'readable_book' => $this->book->book_id,
            'modifiable' => false,
            'is_owner' => false,
            'is_default' => false,
        ]);
        $this->accountGroup = AccountGroup::factory()->create([
            'book_id' => $this->book->book_id,
            'account_type' => AccountService::ACCOUNT_TYPE_ASSET,
            'is_current' => true,
            'account_group_bk_code' => 0,
        ]);
        $this->userWhoDoesNotHaveBook = User::factory()->create();
    }

    public function test_updating_accounts_group_screen_can_be_displayed(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/page/v2/books/'.$this->book->book_id.'/accounts/settings/groups/'.$this->accountGroup->account_group_id);

        $response->assertOk();

        $response->assertSee(__('Edit Account Group'));
    }

    public function test_updating_accounts_group_screen_cannot_be_displayed_because_specified_book_is_invalid(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/page/v2/books/0/accounts/settings/groups/'.$this->accountGroup->account_group_id);

        $response->assertNotFound();
    }

    public function test_updating_accounts_group_screen_cannot_be_displayed_because_specified_accounts_group_is_invalid(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/page/v2/books/'.$this->book->book_id.'/accounts/settings/groups/0');

        $response->assertNotFound();
    }

    public function test_the_user_is_not_allowed_to_display_the_updating_accounts_group_screen(): void
    {
        $response = $this->actingAs($this->userWhoDoesNotHaveBook)
            ->get('/page/v2/books/'.$this->book->book_id.'/accounts/settings/groups/'.$this->accountGroup->account_group_id);

        $response->assertNotFound();
    }

    public function test_user_can_update_accounts_group(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v2/books/'.$this->book->book_id.'/accounts/settings/groups/'.$this->accountGroup->account_group_id, [
                'title' => $this->faker->word(),
                'attribute_current' => 1,
            ]);

        $response->assertOk();

        $response->assertSee(__('Edit Account Group'));
    }

    public function test_the_user_is_not_allowed_to_update_account_group(): void
    {
        $response = $this->actingAs($this->userWhoDoesNotHaveWritePermission)
            ->post('/page/v2/books/'.$this->book->book_id.'/accounts/settings/groups/'.$this->accountGroup->account_group_id, [
                'title' => $this->faker->word(),
                'attribute_current' => 1,
            ]);

        $response->assertOk();

        $response->assertSee(__('Edit Account Group'));
        $response->assertSee(__('You are not permitted to write in this book.'));
    }

    public function test_user_cannot_update_accounts_group_because_specified_accounts_group_is_not_found(): void
    {
        $accountGroupId = (string) Str::uuid();
        $response = $this->actingAs($this->user)
            ->post('/page/v2/books/'.$this->book->book_id.'/accounts/settings/groups/'.$accountGroupId, [
                'title' => $this->faker->word(),
                'attribute_current' => 1,
            ]);

        $response->assertNotFound();
    }

    public function test_user_cannot_update_account_group_because_the_parameters_are_invalid(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v2/books/'.$this->book->book_id.'/accounts/settings/groups/'.$this->accountGroup->account_group_id, []);

        $response->assertOk();

        $response->assertSee(__('Edit Account Group'));
        $response->assertSee(__('Please enter a valid name.'));
    }
}
