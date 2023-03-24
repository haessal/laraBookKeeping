<?php

namespace Tests\Unit\DataProvider\SlipEntryRepositoryInterface;

use App\DataProvider\Eloquent\SlipEntryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class FindByIdTest extends TestCase
{
    use RefreshDatabase;

    protected $slipEntry;

    public function setUp(): void
    {
        parent::setUp();
        $this->slipEntry = new SlipEntryRepository();
    }

    public function test_it_takes_three_arguments_and_returns_an_array_or_null(): void
    {
        $slipEntryId = (string) Str::uuid();
        $bookId = (string) Str::uuid();

        $slipEntries = $this->slipEntry->findById($slipEntryId, $bookId, true);

        if (is_null($slipEntries)) {
            $this->assertNull($slipEntries);
        } else {
            $this->assertIsArray($slipEntries);
        }
    }
}
