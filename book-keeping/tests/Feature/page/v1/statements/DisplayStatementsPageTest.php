<?php

namespace Tests\Feature\page\v1\slip;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Book;
use App\Models\Permission;
use App\Models\Slip;
use App\Models\SlipEntry;
use App\Models\User;
use App\Service\AccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DisplayStatementsPageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\User */
    private $userWhoDoesNotHaveBook;

    /** @var \App\Models\Book */
    private $book;

    /** @var \App\Models\AccountGroup */
    private $accountGroup_asset;

    /** @var \App\Models\AccountGroup */
    private $accountGroup_revenue;

    /** @var \App\Models\Account */
    private $debit;

    /** @var \App\Models\Account */
    private $credit;

    /** @var \App\Models\Slip */
    private $slip;

    /** @var \App\Models\SlipEntry */
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
        $this->accountGroup_asset = AccountGroup::factory()->create([
            'book_id' => $this->book->book_id,
            'account_type' => AccountService::ACCOUNT_TYPE_ASSET,
        ]);
        $this->accountGroup_revenue = AccountGroup::factory()->create([
            'book_id' => $this->book->book_id,
            'account_type' => AccountService::ACCOUNT_TYPE_REVENUE,
        ]);
        $this->debit = Account::factory()->create([
            'account_group_id' => $this->accountGroup_asset->account_group_id,
            'selectable' => true,
        ]);
        $this->credit = Account::factory()->create([
            'account_group_id' => $this->accountGroup_revenue->account_group_id,
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
        $this->userWhoDoesNotHaveBook = User::factory()->create();
    }

    public function test_statements_page_can_be_diplayed(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/page/v1/statements');

        $response->assertOk();
    }

    public function test_statements_page_can_be_diplayed_with_specified_date(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v1/statements', [
                'BEGINNING' => date('Y-m-d'),
                'END' => date('Y-m-d'),
            ]);

        $response->assertOk();
    }

    public function test_statements_page_can_be_diplayed_invalid_parameter(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v1/statements', [
                'BEGINNING' => '',
                'END' => '',
            ]);

        $response->assertOk();

        $response->assertSee('There is no item to be shown.');
    }

    public function test_statements_page_does_not_display_because_default_book_is_not_found(): void
    {
        $response = $this->actingAs($this->userWhoDoesNotHaveBook)
            ->get('/page/v1/statements');

        $response->assertNotFound();
    }
}
