<?php

namespace Tests\Feature\page\v1\slip;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Book;
use App\Models\Permission;
use App\Models\User;
use App\Service\AccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddSlipEntryToDraftSlipTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\Book */
    private $book;

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
            'account_type' => AccountService::ACCOUNT_TYPE_ASSET,
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

    public function test_user_can_add_slip_entry_to_draft_slip(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v1/slip', [
                'debit' => $this->debit->account_id,
                'client' => $this->faker->word(),
                'outline' => $this->faker->word(),
                'credit' => $this->credit->account_id,
                'amount' => $this->faker->numberBetween(1),
                'buttons' => ['add' => __('Add')],
            ]);

        $response->assertOk();

        $response->assertDontSee(__('There is no item.'));
    }
}
