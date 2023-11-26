<?php

namespace Tests\Unit\DataProvider\SlipEntryRepositoryInterface;

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

    public function test_it_takes_one_argument_and_returns_nothing(): void
    {
        $newSlipEntry = [
            'slip_entry_id' => (string) Str::uuid(),
            'slip_id' => (string) Str::uuid(),
            'debit' => (string) Str::uuid(),
            'credit' => (string) Str::uuid(),
            'amount' => 30,
            'client' => 'client31',
            'outline' => 'outline32',
            'display_order' => 0,
            'deleted' => false,
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->slipEntry->createForImporting($newSlipEntry);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(true);
    }
}
