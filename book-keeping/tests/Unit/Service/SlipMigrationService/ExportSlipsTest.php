<?php

namespace Tests\Unit\Service\SlipMigrationService;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\DataProvider\SlipRepositoryInterface;
use App\Service\BookKeepingMigrationTools;
use App\Service\SlipMigrationService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class ExportSlipsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_export_slips(): void
    {
        $bookId = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $slipUpdatedAt_1 = '2024-03-09 17:22:01';
        $slip_1 = [
            'slip_id' => $slipId_1,
            'updated_at' => $slipUpdatedAt_1,
        ];
        $slips_expected = [
            $slipId_1 => [
                'slip_id' => $slipId_1,
                'updated_at' => $slipUpdatedAt_1,
            ],
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('searchBookForExporting')
            ->once()
            ->with($bookId)
            ->andReturn([$slip_1]);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);

        $service = new SlipMigrationService($slipMock, $slipEntryMock, $toolsMock);
        $slips_actual = $service->exportSlips($bookId);

        $this->assertSame($slips_expected, $slips_actual);
    }
}
