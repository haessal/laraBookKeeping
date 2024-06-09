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

class AddAccountsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\User */
    private $userWhoDoesNotHaveWritePermission;

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
    }

    public function test_user_can_add_account_group_which_type_is_asset(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v2/books/'.$this->book->book_id.'/accounts/new', [
                'create' => 'group',
                'accounttype' => AccountService::ACCOUNT_TYPE_ASSET,
                'title' => $this->faker->word(),
            ]);

        $response->assertOk();

        $response->assertDontSee(__('You are not permitted to write in this book.'));
        $response->assertDontSee(__('Please select the type and enter a valid name.'));
    }

    public function test_user_can_add_account_group_which_type_is_expense(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v2/books/'.$this->book->book_id.'/accounts/new', [
                'create' => 'group',
                'accounttype' => AccountService::ACCOUNT_TYPE_EXPENSE,
                'title' => $this->faker->word(),
            ]);

        $response->assertOk();

        $response->assertDontSee(__('You are not permitted to write in this book.'));
        $response->assertDontSee(__('Please select the type and enter a valid name.'));
    }

    public function test_user_can_add_account_group_which_type_is_liability(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v2/books/'.$this->book->book_id.'/accounts/new', [
                'create' => 'group',
                'accounttype' => AccountService::ACCOUNT_TYPE_LIABILITY,
                'title' => $this->faker->word(),
            ]);

        $response->assertOk();

        $response->assertDontSee(__('You are not permitted to write in this book.'));
        $response->assertDontSee(__('Please select the type and enter a valid name.'));
    }

    public function test_user_can_add_account_group_which_type_is_revenue(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v2/books/'.$this->book->book_id.'/accounts/new', [
                'create' => 'group',
                'accounttype' => AccountService::ACCOUNT_TYPE_REVENUE,
                'title' => $this->faker->word(),
            ]);

        $response->assertOk();

        $response->assertDontSee(__('You are not permitted to write in this book.'));
        $response->assertDontSee(__('Please select the type and enter a valid name.'));
    }

    public function test_user_can_add_account_item(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v2/books/'.$this->book->book_id.'/accounts/new', [
                'create' => 'item',
                'accountgroup' => $this->accountGroup->account_group_id,
                'title' => $this->faker->word(),
                'description' => $this->faker->word(),
            ]);

        $response->assertOk();

        $response->assertDontSee(__('You are not permitted to write in this book.'));
        $response->assertDontSee(__('Please select the group and enter a valid name and description.'));
    }

    public function test_the_user_is_not_allowed_to_add_account_group(): void
    {
        $response = $this->actingAs($this->userWhoDoesNotHaveWritePermission)
            ->post('/page/v2/books/'.$this->book->book_id.'/accounts/new', [
                'create' => 'group',
                'accounttype' => AccountService::ACCOUNT_TYPE_ASSET,
                'title' => $this->faker->word(),
            ]);

        $response->assertOk();

        $response->assertSee(__('You are not permitted to write in this book.'));
    }

    public function test_user_cannot_add_account_group_because_the_parameters_are_invalid(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v2/books/'.$this->book->book_id.'/accounts/new', [
                'create' => 'group',
                'accounttype' => '',
                'title' => '',
            ]);

        $response->assertOk();

        $response->assertSee(__('Please select the type and enter a valid name.'));
    }

    public function test_user_cannot_add_account_group_because_the_parameters_are_not_specified(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v2/books/'.$this->book->book_id.'/accounts/new', [
                'create' => 'group',
            ]);

        $response->assertOk();

        $response->assertSee(__('Please select the type and enter a valid name.'));
    }

    public function test_the_user_is_not_allowed_to_add_account_item(): void
    {
        $response = $this->actingAs($this->userWhoDoesNotHaveWritePermission)
            ->post('/page/v2/books/'.$this->book->book_id.'/accounts/new', [
                'create' => 'item',
                'accountgroup' => $this->accountGroup->account_group_id,
                'title' => $this->faker->word(),
                'description' => $this->faker->word(),
            ]);

        $response->assertOK();

        $response->assertSee(__('You are not permitted to write in this book.'));
    }

    public function test_user_cannot_add_account_item_because_the_selected_account_group_is_invalid(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v2/books/'.$this->book->book_id.'/accounts/new', [
                'create' => 'item',
                'accountgroup' => (string) Str::uuid(),
                'title' => $this->faker->word(),
                'description' => $this->faker->word(),
            ]);

        $response->assertNotFound();
    }

    public function test_user_cannot_add_account_item_because_the_parameters_are_invalid(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v2/books/'.$this->book->book_id.'/accounts/new', [
                'create' => 'item',
                'accountgroup' => '',
                'title' => '',
                'description' => '',
            ]);

        $response->assertOK();

        $response->assertSee(__('Please select the group and enter a valid name and description.'));
    }

    public function test_user_cannot_add_account_item_because_the_parameters_are_not_specified(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v2/books/'.$this->book->book_id.'/accounts/new', [
                'create' => 'item',
            ]);

        $response->assertOK();

        $response->assertSee(__('Please select the group and enter a valid name and description.'));
    }

    public function test_user_cannot_add_anything_because_the_group_or_item_as_target_is_not_specified(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v2/books/'.$this->book->book_id.'/accounts/new', []);

        $response->assertNotFound();
    }
}
