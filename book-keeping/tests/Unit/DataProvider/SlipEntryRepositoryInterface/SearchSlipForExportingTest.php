<?php

namespace Tests\Unit\DataProvider\SlipEntryRepositoryInterface;

use App\DataProvider\Eloquent\SlipEntryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_it_takes_one_argument_and_returns_an_array(): void
    {
        $slipId = (string) Str::uuid();

        $slipEntryList = $this->slipEntry->searchSlipForExporting($slipId);

        $this->assertTrue(is_array($slipEntryList));
    }

    public function test_it_takes_two_argument_and_returns_an_array(): void
    {
        $slipId = (string) Str::uuid();
        $slipEntryId = (string) Str::uuid();

        $slipEntryList = $this->slipEntry->searchSlipForExporting($slipId, $slipEntryId);

        $this->assertTrue(is_array($slipEntryList));
    }
}
