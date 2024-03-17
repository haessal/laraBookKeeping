<?php

namespace Tests\Unit\Service\SlipMigrationService;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\DataProvider\SlipRepositoryInterface;
use App\Service\BookKeepingMigrationTools;
use App\Service\SlipMigrationService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class ExportSlipEntriesTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_export_slip_entries(): void
    {
        $bookId = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntryUpdatedAt_1 = '2024-03-09 17:11:10';
        $slip_1 = [
            'slip_id' => $slipId_1,
        ];
        $slipEntry_1 = [
            'slip_entry_id' => $slipEntryId_1,
            'updated_at' => $slipEntryUpdatedAt_1,
        ];
        $slips_expected = [
            $slipId_1 => [
                'entries' => [
                    $slipEntryId_1 => [
                        'slip_entry_id' => $slipEntryId_1,
                        'updated_at' => $slipEntryUpdatedAt_1,
                    ],
                ],
            ],
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('searchBookForExporting')
            ->once()
            ->with($bookId, $slipId_1)
            ->andReturn([$slip_1]);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('searchSlipForExporting')
            ->once()
            ->with($slipId_1)
            ->andReturn([$slipEntry_1]);

        $service = new SlipMigrationService($slipMock, $slipEntryMock, $toolsMock);
        $slips_actual = $service->exportSlipEntries($bookId, $slipId_1);

        $this->assertSame($slips_expected, $slips_actual);
    }
}
