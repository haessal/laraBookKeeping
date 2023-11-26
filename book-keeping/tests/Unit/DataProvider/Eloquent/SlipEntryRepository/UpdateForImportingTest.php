<?php

namespace Tests\Unit\DataProvider\Eloquent\SlipEntryRepository;

use App\DataProvider\Eloquent\SlipEntryRepository;
use App\Models\SlipEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateForImportingTest extends TestCase
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
        $amount = 29;
        $client = 'client30';
        $outline = 'outline31';
        $displayOrder = 1;
        $deleted = false;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipEntryId = SlipEntry::factory()->create([
            'slip_id' => (string) Str::uuid(),
            'debit' => (string) Str::uuid(),
            'credit' => (string) Str::uuid(),
            'amount' => 39,
            'client' => 'client41',
            'outline' => 'outline42',
            'display_order' => 2,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
        $this->slipEntry->updateForImporting($newSlipEntry);
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

    public function test_one_record_is_updated_and_then_deleted(): void
    {
        $slipId = (string) Str::uuid();
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 79;
        $client = 'client80';
        $outline = 'outline81';
        $displayOrder = 1;
        $deleted = true;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipEntryId = SlipEntry::factory()->create([
            'slip_id' => (string) Str::uuid(),
            'debit' => (string) Str::uuid(),
            'credit' => (string) Str::uuid(),
            'amount' => 89,
            'client' => 'client90',
            'outline' => 'outline91',
            'display_order' => 2,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
        $this->slipEntry->updateForImporting($newSlipEntry);
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

    public function test_one_record_is_updated_and_then_restored(): void
    {
        $slipId = (string) Str::uuid();
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 128;
        $client = 'client129';
        $outline = 'outline130';
        $displayOrder = 1;
        $deleted = false;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipEntry = SlipEntry::factory()->create([
            'slip_id' => (string) Str::uuid(),
            'debit' => (string) Str::uuid(),
            'credit' => (string) Str::uuid(),
            'amount' => 138,
            'client' => 'client139',
            'outline' => 'outline140',
            'display_order' => 2,
        ]);
        $slipEntryId = $slipEntry->slip_entry_id;
        $slipEntry->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
        $this->slipEntry->updateForImporting($newSlipEntry);
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

    public function test_one_record_is_updated_and_still_in_the_trash(): void
    {
        $slipId = (string) Str::uuid();
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 180;
        $client = 'client181';
        $outline = 'outline182';
        $displayOrder = 1;
        $deleted = true;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipEntry = SlipEntry::factory()->create([
            'slip_id' => (string) Str::uuid(),
            'debit' => (string) Str::uuid(),
            'credit' => (string) Str::uuid(),
            'amount' => 138,
            'client' => 'client139',
            'outline' => 'outline140',
            'display_order' => 2,
        ]);
        $slipEntryId = $slipEntry->slip_entry_id;
        $slipEntry->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
        $this->slipEntry->updateForImporting($newSlipEntry);
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
