<?php

namespace Tests\Unit\DataProvider\Eloquent\SlipEntryRepository;

use App\DataProvider\Eloquent\SlipEntryRepository;
use App\Models\Slip;
use App\Models\SlipEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchBookTest extends TestCase
{
    use RefreshDatabase;

    protected $slipEntry;

    public function setUp(): void
    {
        parent::setUp();
        $this->slipEntry = new SlipEntryRepository();
    }

    public function test_it_returns_the_slip_entries_with_specified_credit(): void
    {
        $fromDate = '2019-01-01';
        $toDate = '2019-01-31';
        $bookId = (string) Str::uuid();
        $slipOutline = 'slip_outline51';
        $memo = 'memo51';
        $date = '2019-01-10';
        $isDraft = false;
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $accountId4 = (string) Str::uuid();
        $accountId5 = (string) Str::uuid();
        $accountId6 = (string) Str::uuid();
        $accountId7 = (string) Str::uuid();
        $accountId8 = (string) Str::uuid();
        $amount1 = 1000;
        $amount2 = 2000;
        $amount3 = 3000;
        $amount4 = 4000;
        $amount5 = 5000;
        $amount6 = 6000;
        $keyword = 'keyword1';
        $client2 = 'client512';
        $client3 = 'client513';
        $client4 = 'client514';
        $client5 = 'client515';
        $client6 = 'client516';
        $outline1 = 'outline511';
        $outline2 = 'outline512';
        $outline3 = 'outline513';
        $outline5 = 'outline515';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = Slip::factory()->create([
            'book_id'       => $bookId,
            'slip_outline'  => $slipOutline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ])->slip_id;
        $slipEntryId1 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => $amount1,
            'client'        => $keyword,
            'outline'       => $outline1,
        ])->slip_entry_id;
        $slipEntryId2 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId3,
            'amount'        => $amount2,
            'client'        => $client2,
            'outline'       => $outline2,
        ])->slip_entry_id;
        $slipEntryId3 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId4,
            'credit'        => $accountId3,
            'amount'        => $amount3,
            'client'        => $client3,
            'outline'       => $outline3,
        ])->slip_entry_id;
        $slipEntryId4 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId5,
            'credit'        => $accountId3,
            'amount'        => $amount4,
            'client'        => $client4,
            'outline'       => $keyword,
        ])->slip_entry_id;
        $slipEntryId5 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId6,
            'credit'        => $accountId3,
            'amount'        => $amount5,
            'client'        => $client5,
            'outline'       => $outline5,
        ])->slip_entry_id;
        $slipEntryId6 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId7,
            'credit'        => $accountId8,
            'amount'        => $amount6,
            'client'        => $client6,
            'outline'       => $keyword,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $condition = ['credit' => $accountId3];

        $slipEntries = $this->slipEntry->searchBook($bookId, $fromDate, $toDate, $condition);

        $this->assertSame(4, count($slipEntries));
    }

    public function test_it_returns_the_slip_entries_with_specified_debit(): void
    {
        $fromDate = '2019-02-01';
        $toDate = '2019-02-31';
        $bookId = (string) Str::uuid();
        $slipOutline = 'slip_outline52';
        $memo = 'memo52';
        $date = '2019-02-10';
        $isDraft = false;
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $accountId4 = (string) Str::uuid();
        $accountId5 = (string) Str::uuid();
        $accountId6 = (string) Str::uuid();
        $accountId7 = (string) Str::uuid();
        $accountId8 = (string) Str::uuid();
        $amount1 = 1000;
        $amount2 = 2000;
        $amount3 = 3000;
        $amount4 = 4000;
        $amount5 = 5000;
        $amount6 = 6000;
        $keyword = 'keyword2';
        $client2 = 'client522';
        $client3 = 'client523';
        $client4 = 'client524';
        $client5 = 'client525';
        $client6 = 'client526';
        $outline1 = 'outline521';
        $outline2 = 'outline522';
        $outline3 = 'outline523';
        $outline5 = 'outline525';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = Slip::factory()->create([
            'book_id'       => $bookId,
            'slip_outline'  => $slipOutline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ])->slip_id;
        $slipEntryId1 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => $amount1,
            'client'        => $keyword,
            'outline'       => $outline1,
        ])->slip_entry_id;
        $slipEntryId2 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId3,
            'amount'        => $amount2,
            'client'        => $client2,
            'outline'       => $outline2,
        ])->slip_entry_id;
        $slipEntryId3 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId4,
            'credit'        => $accountId3,
            'amount'        => $amount3,
            'client'        => $client3,
            'outline'       => $outline3,
        ])->slip_entry_id;
        $slipEntryId4 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId5,
            'credit'        => $accountId3,
            'amount'        => $amount4,
            'client'        => $client4,
            'outline'       => $keyword,
        ])->slip_entry_id;
        $slipEntryId5 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId6,
            'credit'        => $accountId3,
            'amount'        => $amount5,
            'client'        => $client5,
            'outline'       => $outline5,
        ])->slip_entry_id;
        $slipEntryId6 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId7,
            'credit'        => $accountId8,
            'amount'        => $amount6,
            'client'        => $client6,
            'outline'       => $keyword,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $condition = ['debit' => $accountId1];

        $slipEntries = $this->slipEntry->searchBook($bookId, $fromDate, $toDate, $condition);

        $this->assertSame(2, count($slipEntries));
    }

    public function test_it_returns_the_slip_entries_with_specified_debit_and_credit(): void
    {
        $fromDate = '2019-03-01';
        $toDate = '2019-03-31';
        $bookId = (string) Str::uuid();
        $slipOutline = 'slip_outline53';
        $memo = 'memo53';
        $date = '2019-03-10';
        $isDraft = false;
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $accountId4 = (string) Str::uuid();
        $accountId5 = (string) Str::uuid();
        $accountId6 = (string) Str::uuid();
        $accountId7 = (string) Str::uuid();
        $accountId8 = (string) Str::uuid();
        $amount1 = 1000;
        $amount2 = 2000;
        $amount3 = 3000;
        $amount4 = 4000;
        $amount5 = 5000;
        $amount6 = 6000;
        $keyword = 'keyword3';
        $client2 = 'client532';
        $client3 = 'client533';
        $client4 = 'client534';
        $client5 = 'client535';
        $client6 = 'client536';
        $outline1 = 'outline531';
        $outline2 = 'outline532';
        $outline3 = 'outline533';
        $outline5 = 'outline535';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = Slip::factory()->create([
            'book_id'       => $bookId,
            'slip_outline'  => $slipOutline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ])->slip_id;
        $slipEntryId1 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => $amount1,
            'client'        => $keyword,
            'outline'       => $outline1,
        ])->slip_entry_id;
        $slipEntryId2 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId3,
            'amount'        => $amount2,
            'client'        => $client2,
            'outline'       => $outline2,
        ])->slip_entry_id;
        $slipEntryId3 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId4,
            'credit'        => $accountId3,
            'amount'        => $amount3,
            'client'        => $client3,
            'outline'       => $outline3,
        ])->slip_entry_id;
        $slipEntryId4 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId5,
            'credit'        => $accountId3,
            'amount'        => $amount4,
            'client'        => $client4,
            'outline'       => $keyword,
        ])->slip_entry_id;
        $slipEntryId5 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId6,
            'credit'        => $accountId3,
            'amount'        => $amount5,
            'client'        => $client5,
            'outline'       => $outline5,
        ])->slip_entry_id;
        $slipEntryId6 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId7,
            'credit'        => $accountId8,
            'amount'        => $amount6,
            'client'        => $client6,
            'outline'       => $keyword,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $condition = ['debit' => $accountId1, 'credit' => $accountId3, 'and_or' => 'and'];

        $slipEntries = $this->slipEntry->searchBook($bookId, $fromDate, $toDate, $condition);

        $this->assertSame(1, count($slipEntries));
    }

    public function test_it_returns_the_slip_entries_with_specified_debit_or_credit(): void
    {
        $fromDate = '2019-04-01';
        $toDate = '2019-04-31';
        $bookId = (string) Str::uuid();
        $slipOutline = 'slip_outline54';
        $memo = 'memo54';
        $date = '2019-04-10';
        $isDraft = false;
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $accountId4 = (string) Str::uuid();
        $accountId5 = (string) Str::uuid();
        $accountId6 = (string) Str::uuid();
        $accountId7 = (string) Str::uuid();
        $accountId8 = (string) Str::uuid();
        $amount1 = 1000;
        $amount2 = 2000;
        $amount3 = 3000;
        $amount4 = 4000;
        $amount5 = 5000;
        $amount6 = 6000;
        $keyword = 'keyword4';
        $client2 = 'client542';
        $client3 = 'client543';
        $client4 = 'client544';
        $client5 = 'client545';
        $client6 = 'client546';
        $outline1 = 'outline541';
        $outline2 = 'outline542';
        $outline3 = 'outline543';
        $outline5 = 'outline545';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = Slip::factory()->create([
            'book_id'       => $bookId,
            'slip_outline'  => $slipOutline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ])->slip_id;
        $slipEntryId1 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => $amount1,
            'client'        => $keyword,
            'outline'       => $outline1,
        ])->slip_entry_id;
        $slipEntryId2 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId3,
            'amount'        => $amount2,
            'client'        => $client2,
            'outline'       => $outline2,
        ])->slip_entry_id;
        $slipEntryId3 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId4,
            'credit'        => $accountId3,
            'amount'        => $amount3,
            'client'        => $client3,
            'outline'       => $outline3,
        ])->slip_entry_id;
        $slipEntryId4 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId5,
            'credit'        => $accountId3,
            'amount'        => $amount4,
            'client'        => $client4,
            'outline'       => $keyword,
        ])->slip_entry_id;
        $slipEntryId5 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId6,
            'credit'        => $accountId3,
            'amount'        => $amount5,
            'client'        => $client5,
            'outline'       => $outline5,
        ])->slip_entry_id;
        $slipEntryId6 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId7,
            'credit'        => $accountId8,
            'amount'        => $amount6,
            'client'        => $client6,
            'outline'       => $keyword,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $condition = ['debit' => $accountId1, 'credit' => $accountId3, 'and_or' => 'or'];

        $slipEntries = $this->slipEntry->searchBook($bookId, $fromDate, $toDate, $condition);

        $this->assertSame(5, count($slipEntries));
    }

    public function test_it_returns_the_slip_entries_with_specified_keyword(): void
    {
        $fromDate = '2019-05-01';
        $toDate = '2019-05-31';
        $bookId = (string) Str::uuid();
        $slipOutline = 'slip_outline55';
        $memo = 'memo55';
        $date = '2019-05-10';
        $isDraft = false;
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $accountId4 = (string) Str::uuid();
        $accountId5 = (string) Str::uuid();
        $accountId6 = (string) Str::uuid();
        $accountId7 = (string) Str::uuid();
        $accountId8 = (string) Str::uuid();
        $amount1 = 1000;
        $amount2 = 2000;
        $amount3 = 3000;
        $amount4 = 4000;
        $amount5 = 5000;
        $amount6 = 6000;
        $keyword = 'keyword5';
        $client2 = 'client552';
        $client3 = 'client553';
        $client4 = 'client554';
        $client5 = 'client555';
        $client6 = 'client556';
        $outline1 = 'outline551';
        $outline2 = 'outline552';
        $outline3 = 'outline553';
        $outline5 = 'outline555';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = Slip::factory()->create([
            'book_id'       => $bookId,
            'slip_outline'  => $slipOutline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ])->slip_id;
        $slipEntryId1 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => $amount1,
            'client'        => $keyword,
            'outline'       => $outline1,
        ])->slip_entry_id;
        $slipEntryId2 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId3,
            'amount'        => $amount2,
            'client'        => $client2,
            'outline'       => $outline2,
        ])->slip_entry_id;
        $slipEntryId3 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId4,
            'credit'        => $accountId3,
            'amount'        => $amount3,
            'client'        => $client3,
            'outline'       => $outline3,
        ])->slip_entry_id;
        $slipEntryId4 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId5,
            'credit'        => $accountId3,
            'amount'        => $amount4,
            'client'        => $client4,
            'outline'       => $keyword,
        ])->slip_entry_id;
        $slipEntryId5 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId6,
            'credit'        => $accountId3,
            'amount'        => $amount5,
            'client'        => $client5,
            'outline'       => $outline5,
        ])->slip_entry_id;
        $slipEntryId6 = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId7,
            'credit'        => $accountId8,
            'amount'        => $amount6,
            'client'        => $client6,
            'outline'       => $keyword,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $condition = ['keyword' => $keyword];

        $slipEntries = $this->slipEntry->searchBook($bookId, $fromDate, $toDate, $condition);

        $this->assertSame(3, count($slipEntries));
    }

    public function test_the_returned_array_has_keys_as_slip_entry(): void
    {
        $fromDate = '2019-09-15';
        $toDate = '2019-09-30';
        $bookId = (string) Str::uuid();
        $slipOutline = 'slip_outline4';
        $memo = 'memo4';
        $date = '2019-09-16';
        $isDraft = false;
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 4000;
        $client = 'client4';
        $outline = 'outline4';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = Slip::factory()->create([
            'book_id'       => $bookId,
            'slip_outline'  => $slipOutline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ])->slip_id;
        $slipEntryId = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $debit,
            'credit'        => $credit,
            'amount'        => $amount,
            'client'        => $client,
            'outline'       => $outline,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $slipEntries = $this->slipEntry->searchBook($bookId, $fromDate, $toDate, []);

        $this->assertFalse(count($slipEntries) === 0);
        if (! (count($slipEntries) === 0)) {
            $this->assertSame([
                'slip_id',
                'date',
                'slip_outline',
                'slip_memo',
                'slip_entry_id',
                'debit',
                'credit',
                'amount',
                'client',
                'outline',
            ], array_keys($slipEntries[0]));
        }
    }
}
