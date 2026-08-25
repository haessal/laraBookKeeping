<?php

namespace Tests\Unit\Repositories\SlipRepositoryInterface;

use App\Repositories\Eloquent\SlipRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchBookForExportingTest extends TestCase
{
    use RefreshDatabase;

    /** @var SlipRepository */
    protected $slip;

    public function setUp(): void
    {
        parent::setUp();
        $this->slip = new SlipRepository();
    }

    public function test_it_takes_one_argument_and_returns_an_array(): void
    {
        $bookId = (string) Str::uuid();

        $slipList = $this->slip->searchBookForExporting($bookId);

        $this->assertTrue(is_array($slipList));
    }

    public function test_it_takes_two_argument_and_returns_an_array(): void
    {
        $bookId = (string) Str::uuid();
        $slipId = (string) Str::uuid();

        $slipList = $this->slip->searchBookForExporting($bookId, $slipId);

        $this->assertTrue(is_array($slipList));
    }
}
