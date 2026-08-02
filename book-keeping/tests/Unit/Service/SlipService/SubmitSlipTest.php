<?php

namespace Tests\Unit\Service\SlipService;

use App\Repositories\SlipEntryRepositoryInterface;
use App\Repositories\SlipRepositoryInterface;
use App\Service\SlipService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class SubmitSlipTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_calls_repository_to_submit_the_slip(): void
    {
        $slipId = (string) Str::uuid();
        /** @var \App\Repositories\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('updateDraftMark')
            ->once()
            ->with($slipId, false);
        /** @var \App\Repositories\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slip->submitSlip($slipId);

        $this->assertTrue(true);
    }
}
