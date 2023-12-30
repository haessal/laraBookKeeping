<?php

namespace Tests\Unit\DataProvider\Eloquent\SlipEntryRepository;

use App\DataProvider\Eloquent\SlipEntryRepository;
use App\Models\Slip;
use App\Models\SlipEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class FindByIdTest extends TestCase
{
    use RefreshDatabase;

    protected $slipEntry;

    public function setUp(): void
    {
        parent::setUp();
        $this->slipEntry = new SlipEntryRepository();
    }

    public function test_it_returns_the_slip_entry(): void
    {
        $bookId = (string) Str::uuid();
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $date = '2020-05-31';
        $slip_outline = 'slip_outline208';
        $amount = 36912;
        $client = 'client7';
        $outline = 'outline7';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = Slip::factory()->create([
            'book_id' => $bookId,
            'slip_outline' => $slip_outline,
            'slip_memo' => null,
            'date' => $date,
            'is_draft' => true,
        ])->slip_id;
        $slipEntryId = SlipEntry::factory()->create([
            'slip_id' => $slipId,
            'debit' => $accountId1,
            'credit' => $accountId2,
            'amount' => $amount,
            'client' => $client,
            'outline' => $outline,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $slipEntry_expected = [
            'slip_id' => $slipId,
            'date' => $date,
            'slip_outline' => $slip_outline,
            'slip_memo' => null,
            'slip_entry_id' => $slipEntryId,
            'debit' => $accountId1,
            'credit' => $accountId2,
            'amount' => $amount,
            'client' => $client,
            'outline' => $outline,
        ];

        $slipEntry_actual = $this->slipEntry->findById($slipEntryId, $bookId, true);

        $this->assertSame($slipEntry_expected, $slipEntry_actual);
    }

    public function test_it_returns_null_because_the_slip_is_draft(): void
    {
        $bookId = (string) Str::uuid();
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $amount = 36912;
        $client = 'client7';
        $outline = 'outline7';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = Slip::factory()->create([
            'book_id' => $bookId,
            'slip_outline' => 'slip_outline208',
            'date' => '2020-05-31',
            'is_draft' => true,
        ])->slip_id;
        $slipEntryId = SlipEntry::factory()->create([
            'slip_id' => $slipId,
            'debit' => $accountId1,
            'credit' => $accountId2,
            'amount' => $amount,
            'client' => $client,
            'outline' => $outline,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $slipEntry_actual = $this->slipEntry->findById($slipEntryId, $bookId, false);

        $this->assertNull($slipEntry_actual);
    }
}
