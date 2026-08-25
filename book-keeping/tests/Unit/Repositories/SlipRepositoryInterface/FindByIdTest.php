<?php

namespace Tests\Unit\Repositories\SlipRepositoryInterface;

use App\Repositories\Eloquent\SlipRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class FindByIdTest extends TestCase
{
    use RefreshDatabase;

    /** @var SlipRepository */
    protected $slip;

    public function setUp(): void
    {
        parent::setUp();
        $this->slip = new SlipRepository();
    }

    public function test_it_takes_two_arguments_and_returns_an_array_or_null(): void
    {
        $bookId = (string) Str::uuid();
        $slipId = (string) Str::uuid();

        $slip = $this->slip->findById($slipId, $bookId);

        if (is_null($slip)) {
            $this->assertNull($slip);
        } else {
            $this->assertIsArray($slip);
        }
    }
}
