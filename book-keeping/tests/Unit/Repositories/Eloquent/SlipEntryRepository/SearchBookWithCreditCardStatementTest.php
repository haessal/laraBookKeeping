<?php

namespace Tests\Unit\Repositories\Eloquent\SlipEntryRepository;

use App\Models\DataProvider\Eloquent\Slip;
use App\Models\DataProvider\Eloquent\SlipEntry;
use App\Repositories\Eloquent\SlipEntryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchBookWithCreditCardStatementTest extends TestCase
{
    use RefreshDatabase;

    /** @var SlipEntryRepository */
    protected $slipEntry;

    public function setUp(): void
    {
        parent::setUp();
        $this->slipEntry = new SlipEntryRepository();
    }

    public function test_it_returns_the_slip_entries_registered_in_specified_credit(): void
    {
        $bookId = (string) Str::uuid();
        $creditCardStatementId = (string) Str::uuid();
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $slipOutline = 'slip_outline31';
        $memo = 'memo32';
        $date = '2024-07-07';
        $isDraft = false;
        $amount1 = 3500;
        $keyword = 'keyword36';
        $outline1 = 'outline37';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = Slip::factory()->create([
            'book_id' => $bookId,
            'slip_outline' => $slipOutline,
            'slip_memo' => $memo,
            'date' => $date,
            'is_draft' => $isDraft,
        ])->slip_id;
        $slipEntryId1 = SlipEntry::factory()->create([
            'slip_id' => $slipId,
            'debit' => $accountId1,
            'credit' => $accountId2,
            'amount' => $amount1,
            'client' => $keyword,
            'outline' => $outline1,
            'credit_card_statement_id' => $creditCardStatementId,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $slipEntries = $this->slipEntry->searchBookWithCreditCardStatement($bookId, $creditCardStatementId);

        $this->assertSame(1, count($slipEntries));
    }
}
