<?php

namespace Tests\Unit\DataProvider\SlipEntryRepositoryInterface;

use App\DataProvider\Eloquent\SlipEntryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchSlipTest extends TestCase
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

        $slipEntries = $this->slipEntry->searchSlip($slipId);

        $this->assertIsArray($slipEntries);
    }
}
