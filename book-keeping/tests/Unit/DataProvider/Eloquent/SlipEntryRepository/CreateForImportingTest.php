<?php

namespace Tests\Unit\DataProvider\Eloquent\SlipEntryRepository;

use App\DataProvider\Eloquent\SlipEntryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateForImportingTest extends TestCase
{
    use RefreshDatabase;

    protected $slipEntry;

    public function setUp(): void
    {
        parent::setUp();
        $this->slipEntry = new SlipEntryRepository();
    }

    public function test_one_record_is_created(): void
    {
        $slipEntryId = (string) Str::uuid();
        $slipId = (string) Str::uuid();
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 29;
        $client = 'client30';
        $outline = 'outline31';
        $displayOrder = 1;
        $deleted = false;
        $newSlipEntry = [
            'slip_entry_id' => $slipEntryId,
            'slip_id' => $slipId,
            'debit' => $debit,
            'credit' => $credit,
            'amount' => $amount,
            'client' => $client,
            'outline' => $outline,
            'display_order' => $displayOrder,
            'deleted' => $deleted,
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->slipEntry->createForImporting($newSlipEntry);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_slip_entries', [
            'slip_entry_id' => $slipEntryId,
            'slip_id' => $slipId,
            'debit' => $debit,
            'credit' => $credit,
            'amount' => $amount,
            'client' => $client,
            'outline' => $outline,
            'display_order' => $displayOrder,
            'deleted_at' => null,
        ]);
    }

    public function test_one_record_is_created_and_then_deleted(): void
    {
        $slipEntryId = (string) Str::uuid();
        $slipId = (string) Str::uuid();
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 69;
        $client = 'client70';
        $outline = 'outline71';
        $displayOrder = 2;
        $deleted = true;
        $newSlipEntry = [
            'slip_entry_id' => $slipEntryId,
            'slip_id' => $slipId,
            'debit' => $debit,
            'credit' => $credit,
            'amount' => $amount,
            'client' => $client,
            'outline' => $outline,
            'display_order' => $displayOrder,
            'deleted' => $deleted,
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->slipEntry->createForImporting($newSlipEntry);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertSoftDeleted('bk2_0_slip_entries', [
            'slip_entry_id' => $slipEntryId,
            'slip_id' => $slipId,
            'debit' => $debit,
            'credit' => $credit,
            'amount' => $amount,
            'client' => $client,
            'outline' => $outline,
            'display_order' => $displayOrder,
        ]);
    }
}
