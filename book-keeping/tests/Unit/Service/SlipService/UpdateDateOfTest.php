<?php

namespace Tests\Unit\Service\SlipService;

use App\Repositories\SlipEntryRepositoryInterface;
use App\Repositories\SlipRepositoryInterface;
use App\Service\SlipService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class UpdateDateOfTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_calls_repository_to_update_the_date_of_the_slip(): void
    {
        $slipId = (string) Str::uuid();
        $date = '2019-11-01';
        /** @var \App\Repositories\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('update')
            ->once()
            ->with($slipId, ['date' => $date]);
        /** @var \App\Repositories\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slip->updateDateOf($slipId, $date);

        $this->assertTrue(true);
    }
}
