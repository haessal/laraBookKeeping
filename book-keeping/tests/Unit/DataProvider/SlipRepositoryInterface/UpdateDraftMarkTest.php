<?php

namespace Tests\Unit\DataProvider\SlipRepositoryInterface;

use App\DataProvider\Eloquent\SlipRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateDraftMarkTest extends TestCase
{
    use RefreshDatabase;

    protected $slip;

    public function setUp(): void
    {
        parent::setUp();
        $this->slip = new SlipRepository();
    }

    public function test_it_takes_two_arguments_and_returns_nothing(): void
    {
        $slipId = (string) Str::uuid();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->slip->updateDraftMark($slipId, true);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(true);
    }
}
