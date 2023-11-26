<?php

namespace Tests\Unit\DataProvider\Eloquent\SlipEntryRepository;

use App\DataProvider\Eloquent\SlipEntryRepository;
use App\Models\SlipEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchSlipForExportingTest extends TestCase
{
    use RefreshDatabase;

    protected $slipEntry;

    public function setUp(): void
    {
        parent::setUp();
        $this->slipEntry = new SlipEntryRepository();
    }

    public function test_the_returned_array_has_keys_as_exported_slip_entry(): void
    {
        $slipId = (string) Str::uuid();
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 29;
        $client = 'client30';
        $outline = 'outline31';
        $displayOrder = 1;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        SlipEntry::factory()->create([
            'slip_id' => $slipId,
            'debit' => $debit,
            'credit' => $credit,
            'amount' => $amount,
            'client' => $client,
            'outline' => $outline,
            'display_order' => $displayOrder,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $slipEntryList = $this->slipEntry->searchSlipForExporting($slipId);

        $this->assertFalse(count($slipEntryList) === 0);
        if (! (count($slipEntryList) === 0)) {
            $this->assertSame([
                'slip_entry_id',
                'slip_id',
                'debit',
                'credit',
                'amount',
                'client',
                'outline',
                'display_order',
                'created_at',
                'updated_at',
                'deleted_at',
            ], array_keys($slipEntryList[0]));
        }
    }

    public function test_the_returned_array_has_keys_as_exported_slip_entry_even_if_it_is_called_with_slip_entry_id(): void
    {
        $slipId = (string) Str::uuid();
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 70;
        $client = 'client71';
        $outline = 'outline72';
        $displayOrder = 1;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipEntryId = SlipEntry::factory()->create([
            'slip_id' => $slipId,
            'debit' => $debit,
            'credit' => $credit,
            'amount' => $amount,
            'client' => $client,
            'outline' => $outline,
            'display_order' => $displayOrder,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $slipEntryList = $this->slipEntry->searchSlipForExporting($slipId, $slipEntryId);

        $this->assertFalse(count($slipEntryList) === 0);
        if (! (count($slipEntryList) === 0)) {
            $this->assertSame([
                'slip_entry_id',
                'slip_id',
                'debit',
                'credit',
                'amount',
                'client',
                'outline',
                'display_order',
                'created_at',
                'updated_at',
                'deleted_at',
            ], array_keys($slipEntryList[0]));
        }
    }
}
