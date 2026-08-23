<?php

namespace Tests\Feature\page\v1\findslips;

use App\Models\DataProvider\Eloquent\Account;
use App\Models\DataProvider\Eloquent\AccountGroup;
use App\Models\DataProvider\Eloquent\Book;
use App\Models\DataProvider\Eloquent\Permission;
use App\Models\DataProvider\Eloquent\Slip;
use App\Models\DataProvider\Eloquent\SlipEntry;
use App\Models\User;
use App\Service\AccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchForSlipsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\DataProvider\Eloquent\Book */
    private $book;

    /** @var \App\Models\DataProvider\Eloquent\AccountGroup */
    private $accountGroup;

    /** @var \App\Models\DataProvider\Eloquent\Account */
    private $debit;

    /** @var \App\Models\DataProvider\Eloquent\Account */
    private $credit;

    /** @var \App\Models\DataProvider\Eloquent\Slip */
    private $slip;

    /** @var \App\Models\DataProvider\Eloquent\SlipEntry */
    private $slipEntry;

    /** @var string */
    private $today;

    public function setup(): void
    {
        $this->today = date('Y-m-d');
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
        $this->slip = Slip::factory()->create([
            'book_id' => $this->book->book_id,
            'date' => $this->today,
            'is_draft' => false,
        ]);
        $this->slipEntry = SlipEntry::factory()->create([
            'slip_id' => $this->slip->slip_id,
            'debit' => $this->debit->account_id,
            'credit' => $this->credit->account_id,
        ]);
    }

    public function test_user_can_search_for_slips(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v1/findslips', [
                'BEGINNING' => $this->today,
                'buttons' => ['search' => __('Search')],
            ]);

        $response->assertOk();

        $response->assertDontSee(__('No items that match the condition.'));
    }

    public function test_user_cannot_find_slips_that_match_the_condition(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v1/findslips', [
                'KEYWORD' => 'sample',
                'buttons' => ['search' => __('Search')],
            ]);

        $response->assertOk();

        $response->assertSee(__('No items that match the condition.'));
    }

    public function test_user_cannot_find_slips_because_of_invalid_parameter(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v1/findslips', [
                'BEGINNING' => '2024-05-1',
                'buttons' => ['search' => __('Search')],
            ]);

        $response->assertOk();

        $response->assertSee(__('Invalid date format.'));
    }
}
