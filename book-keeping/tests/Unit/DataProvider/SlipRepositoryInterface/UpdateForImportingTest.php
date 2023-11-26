<?php

namespace Tests\Unit\DataProvider\SlipRepositoryInterface;

use App\DataProvider\Eloquent\SlipRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateForImportingTest extends TestCase
{
    use RefreshDatabase;

    protected $slip;

    public function setUp(): void
    {
        parent::setUp();
        $this->slip = new SlipRepository();
    }

    public function test_it_takes_one_argument_and_returns_nothing(): void
    {
        $newSlip = [
            'slip_id' => (string) Str::uuid(),
            'book_id' => (string) Str::uuid(),
            'slip_outline' => 'outline',
            'slip_memo' => 'memo',
            'date' => '2023-12-30',
            'is_draft' => false,
            'display_order' => 0,
            'deleted' => false,
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->slip->updateForImporting($newSlip);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(true);
    }
}
