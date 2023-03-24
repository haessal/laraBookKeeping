<?php

namespace Tests\Unit\DataProvider\Eloquent\SlipEntryRepository;

use App\DataProvider\Eloquent\SlipEntryRepository;
use App\Models\SlipEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    protected $slipEntry;

    public function setUp(): void
    {
        parent::setUp();
        $this->slipEntry = new SlipEntryRepository();
    }

    public function test_one_record_is_soft_deleted(): void
    {
        $slipId = (string) Str::uuid();
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $client = 'client5';
        $outline = 'outline5';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipEntryId = SlipEntry::factory()->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => 1234,
            'client'        => $client,
            'outline'       => $outline,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->slipEntry->delete($slipEntryId);

        $this->assertSoftDeleted('bk2_0_slip_entries', [
            'slip_entry_id' => $slipEntryId,
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => 1234,
            'client'        => $client,
            'outline'       => $outline,
        ]);
    }
}
