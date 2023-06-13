<?php

namespace Tests\Unit\Service\SlipService;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\DataProvider\SlipRepositoryInterface;
use App\Service\SlipService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class UpdateSlipTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_update_the_slip(): void
    {
        $slipId = (string) Str::uuid();
        $newData = ['outline' => 'outline431'];
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('update')
            ->once()
            ->with($slipId, $newData);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slip->updateSlip($slipId, $newData);

        $this->assertTrue(true);
    }
}