<?php

namespace Tests\Unit\DataProvider\Eloquent\SlipEntryRepository;

use App\DataProvider\Eloquent\SlipEntryRepository;
use App\Models\Slip;
use App\Models\SlipEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchBookAndCalculateSumTest extends TestCase
{
    use RefreshDatabase;

    protected $slipEntry;

    public function setUp(): void
    {
        parent::setUp();
        $this->slipEntry = new SlipEntryRepository();
    }

    public function test_it_returns_pairs_of_debit_and_credit(): void
    {
        $fromDate = '2019-08-01';
        $toDate = '2019-08-31';
        $bookId = (string) Str::uuid();
        $slipOutline = 'slip_outline10';
        $memo = 'memo10';
        $date = '2019-08-02';
        $isDraft = false;
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $client = 'client10';
        $outline = 'outline10';
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
            'amount' => 10,
            'client' => $client,
            'outline' => $outline,
        ])->slip_entry_id;
        $slipEntryId2 = SlipEntry::factory()->create([
            'slip_id' => $slipId,
            'debit' => $accountId1,
            'credit' => $accountId3,
            'amount' => 200,
            'client' => $client,
            'outline' => $outline,
        ])->slip_entry_id;
        $slipEntryId3 = SlipEntry::factory()->create([
            'slip_id' => $slipId,
            'debit' => $accountId2,
            'credit' => $accountId3,
            'amount' => 3000,
            'client' => $client,
            'outline' => $outline,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $sumList = $this->slipEntry->searchBookAndCalculateSum($bookId, $fromDate, $toDate);

        $this->assertTrue(count($sumList) === 3);
        if (count($sumList) === 3) {
            $this->assertSame(210, $sumList[$accountId1]['debit']);
            $this->assertSame(0, $sumList[$accountId1]['credit']);
            $this->assertSame(3000, $sumList[$accountId2]['debit']);
            $this->assertSame(10, $sumList[$accountId2]['credit']);
            $this->assertSame(0, $sumList[$accountId3]['debit']);
            $this->assertSame(3200, $sumList[$accountId3]['credit']);
        }
    }
}
