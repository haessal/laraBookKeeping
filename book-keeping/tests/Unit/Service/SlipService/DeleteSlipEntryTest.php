<?php

namespace Tests\Unit\Service\SlipService;

use App\Repositories\SlipEntryRepositoryInterface;
use App\Repositories\SlipRepositoryInterface;
use App\Service\SlipService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class DeleteSlipEntryTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_calls_repository_to_delete_the_slip_entry(): void
    {
        $slipEntryId = (string) Str::uuid();
        /** @var \App\Repositories\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        /** @var \App\Repositories\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('delete')
            ->once()
            ->with($slipEntryId);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slip->deleteSlipEntry($slipEntryId);

        $this->assertTrue(true);
    }
}
