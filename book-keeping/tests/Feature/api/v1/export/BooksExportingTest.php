<?php

namespace Tests\Feature\api\v1\export;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Book;
use App\Models\Permission;
use App\Models\Slip;
use App\Models\SlipEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BooksExportingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\Book */
    private $book;

    /** @var \App\Models\AccountGroup */
    private $accountGroup;

    /** @var \App\Models\Account */
    private $accountItem;

    /** @var \App\Models\Account */
    //private $credit;

    /** @var \App\Models\Slip */
    private $slip;

    /** @var \App\Models\SlipEntry */
    private $slipEntry;

    public function setup(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->book = Book::factory()->create();
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
        $this->accountItem = Account::factory()->create([
            'account_group_id' => $this->accountGroup->account_group_id,
        ]);
        $this->slip = Slip::factory()->create([
            'book_id' => $this->book->book_id,
        ]);
        $this->slipEntry = SlipEntry::factory()->create([
            'slip_id' => $this->slip->slip_id,
            'debit' => $this->accountItem->account_id,
            'credit' => $this->accountItem->account_id,
        ]);
    }

    public function test_books_can_be_exported(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/api/v1/export/books');

        $bookUpdatedAt = $this->book->updated_at->toJSON();
        $response->assertOk()
            ->assertJson([
                'version' => '2.0',
                'books' => [
                    [
                        'book_id' => $this->book->book_id,
                        'book' => [
                            'book_id' => $this->book->book_id,
                            'updated_at' => $bookUpdatedAt,
                        ],
                    ],
                ],
            ]);
    }

    public function test_books_can_be_exported_as_dump(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/api/v1/export/books?mode=dump');

        $bookUpdatedAt = $this->book->updated_at->toJSON();
        $accountGroupUpdatedAt = $this->accountGroup->updated_at->toJSON();
        $accountItemUpdatedAt = $this->accountItem->updated_at->toJSON();
        $slipUpdatedAt = $this->slip->updated_at->toJSON();
        $slipEntryUpdatedAt = $this->slipEntry->updated_at->toJSON();
        $response->assertOk()
            ->assertJson([
                'version' => '2.0',
                'books' => [
                    [
                        'book_id' => $this->book->book_id,
                        'book' => [
                            'book_id' => $this->book->book_id,
                            'book_name' => $this->book->book_name,
                            'display_order' => $this->book->display_order,
                            'updated_at' => $bookUpdatedAt,
                            'deleted' => false,
                        ],
                        'accounts' => [
                            [
                                'account_group_id' => $this->accountGroup->account_group_id,
                                'account_group' => [
                                    'account_group_id' => $this->accountGroup->account_group_id,
                                    'book_id' => $this->accountGroup->book_id,
                                    'account_type' => $this->accountGroup->account_type,
                                    'account_group_title' => $this->accountGroup->account_group_title,
                                    'bk_uid' => $this->accountGroup->bk_uid,
                                    'account_group_bk_code' => $this->accountGroup->account_group_bk_code,
                                    'is_current' => intval($this->accountGroup->is_current),
                                    'display_order' => $this->accountGroup->display_order,
                                    'updated_at' => $accountGroupUpdatedAt,
                                    'deleted' => false,
                                ],
                                'items' => [
                                    [
                                        'account_id' => $this->accountItem->account_id,
                                        'account' => [
                                            'account_id' => $this->accountItem->account_id,
                                            'account_group_id' => $this->accountItem->account_group_id,
                                            'account_title' => $this->accountItem->account_title,
                                            'description' => $this->accountItem->description,
                                            'selectable' => intval($this->accountItem->selectable),
                                            'bk_uid' => $this->accountItem->bk_uid,
                                            'account_bk_code' => $this->accountItem->account_bk_code,
                                            'display_order' => $this->accountItem->display_order,
                                            'updated_at' => $accountItemUpdatedAt,
                                            'deleted' => false,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'slips' => [
                            [
                                'slip_id' => $this->slip->slip_id,
                                'slip' => [
                                    'slip_id' => $this->slip->slip_id,
                                    'book_id' => $this->slip->book_id,
                                    'slip_outline' => $this->slip->slip_outline,
                                    'slip_memo' => $this->slip->slip_memo,
                                    'date' => $this->slip->date,
                                    'is_draft' => intval($this->slip->is_draft),
                                    'display_order' => $this->slip->display_order,
                                    'updated_at' => $slipUpdatedAt,
                                    'deleted' => false,
                                ],
                                'entries' => [
                                    [
                                        'slip_entry_id' => $this->slipEntry->slip_entry_id,
                                        'slip_entry' => [
                                            'slip_entry_id' => $this->slipEntry->slip_entry_id,
                                            'slip_id' => $this->slipEntry->slip_id,
                                            'debit' => $this->slipEntry->debit,
                                            'credit' => $this->slipEntry->credit,
                                            'amount' => $this->slipEntry->amount,
                                            'client' => $this->slipEntry->client,
                                            'outline' => $this->slipEntry->outline,
                                            'display_order' => $this->slipEntry->display_order,
                                            'updated_at' => $slipEntryUpdatedAt,
                                            'deleted' => false,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
    }
}
