<?php

namespace Tests\Unit\Repositories\Eloquent\SlipEntryRepository;

use App\Models\DataProvider\Eloquent\SlipEntry;
use App\Repositories\Eloquent\SlipEntryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchSlipTest extends TestCase
{
    use RefreshDatabase;

    /** @var SlipEntryRepository */
    protected $slipEntry;

    public function setUp(): void
    {
        parent::setUp();
        $this->slipEntry = new SlipEntryRepository();
    }

    public function test_it_returns_the_slip_entry(): void
    {
        $slipId = (string) Str::uuid();
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $amount = 2468;
        $client = 'client6';
        $outline = 'outline6';
        $creditCardStatementId = (string) Str::uuid();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipEntryId = SlipEntry::factory()->create([
            'slip_id' => $slipId,
            'debit' => $accountId1,
            'credit' => $accountId2,
            'amount' => $amount,
            'client' => $client,
            'outline' => $outline,
            'credit_card_statement_id' => $creditCardStatementId,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $slipEntries_expected = [
            [
                'slip_entry_id' => $slipEntryId,
                'slip_id' => $slipId,
                'debit' => $accountId1,
                'credit' => $accountId2,
                'amount' => $amount,
                'client' => $client,
                'outline' => $outline,
                'credit_card_statement_id' => $creditCardStatementId,
            ],
        ];

        $slipEntries_actual = $this->slipEntry->searchSlip($slipId);

        $this->assertSame($slipEntries_expected, $slipEntries_actual);
    }
}
