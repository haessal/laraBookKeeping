<?php

namespace Tests\Unit\DataProvider\Eloquent\SlipEntryRepository;

use App\DataProvider\Eloquent\SlipEntryRepository;
use App\Models\SlipEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    protected $slipEntry;

    public function setUp(): void
    {
        parent::setUp();
        $this->slipEntry = new SlipEntryRepository();
    }

    public function test_one_record_is_updated(): void
    {
        $slipId = (string) Str::uuid();
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 836;
        $client = 'client837';
        $outline = 'outlin838';
        $debit_updated = (string) Str::uuid();
        $credit_updated = (string) Str::uuid();
        $amount_updated = 841;
        $client_updated = 'client_updated842';
        $outline_updated = 'outlin_updated843';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipEntryId = SlipEntry::factory()->create([
            'slip_id' => $slipId,
            'debit' => $debit,
            'credit' => $credit,
            'amount' => $amount,
            'client' => $client,
            'outline' => $outline,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->slipEntry->update($slipEntryId, [
            'debit' => $debit_updated,
            'credit' => $credit_updated,
            'amount' => $amount_updated,
            'client' => $client_updated,
            'outline' => $outline_updated,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_slip_entries', [
            'slip_entry_id' => $slipEntryId,
            'slip_id' => $slipId,
            'debit' => $debit_updated,
            'credit' => $credit_updated,
            'amount' => $amount_updated,
            'client' => $client_updated,
            'outline' => $outline_updated,
        ]);
    }
}
