<?php

namespace Tests\Unit\Service\SlipMigrationService;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\DataProvider\SlipRepositoryInterface;
use App\Service\BookKeepingMigrationTools;
use App\Service\SlipMigrationService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class DumpSlipsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_export_slips_as_dump(): void
    {
        $bookId = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $slipOutline_1 = 'outline24';
        $slipMemo_1 = 'memo2525';
        $slipDate = '2023-12-10';
        $isDraft_1 = false;
        $slipDisplayOrder_1 = 2;
        $slipUpdatedAt_1 = '2023-12-10 13:30:01';
        $slipEntryId_1 = (string) Str::uuid();
        $debit_1 = (string) Str::uuid();
        $credit_1 = (string) Str::uuid();
        $amount_1 = 3840;
        $client_1 = 'client35';
        $outline_1 = 'outline36';
        $slipEntryDisplayOrder_1 = 4;
        $slipEntryUpdatedAt_1 = '2023-12-10 13:30:02';
        $slip_1 = [
            'slip_id' => $slipId_1,
            'book_id' => $bookId,
            'slip_outline' => $slipOutline_1,
            'slip_memo' => $slipMemo_1,
            'date' => $slipDate,
            'is_draft' => $isDraft_1,
            'display_order' => $slipDisplayOrder_1,
            'created_at' => '2023-12-10 11:02:01',
            'updated_at' => $slipUpdatedAt_1,
            'deleted_at' => null,
        ];
        $convertedSlip_1 = [
            'slip_id' => $slipId_1,
            'book_id' => $bookId,
            'slip_outline' => $slipOutline_1,
            'slip_memo' => $slipMemo_1,
            'date' => $slipDate,
            'is_draft' => $isDraft_1,
            'display_order' => $slipDisplayOrder_1,
            'updated_at' => $slipUpdatedAt_1,
            'deleted' => false,
        ];
        $slipEntry_1 = [
            'slip_entry_id' => $slipEntryId_1,
            'slip_id' => $slipId_1,
            'debit' => $debit_1,
            'credit' => $credit_1,
            'amount' => $amount_1,
            'client' => $client_1,
            'outline' => $outline_1,
            'display_order' => $slipEntryDisplayOrder_1,
            'created_at' => '2023-12-10 11:02:04',
            'updated_at' => $slipEntryUpdatedAt_1,
            'deleted_at' => null,
        ];
        $convertedSlipEntry_1 = [
            'slip_entry_id' => $slipEntryId_1,
            'slip_id' => $slipId_1,
            'debit' => $debit_1,
            'credit' => $credit_1,
            'amount' => $amount_1,
            'client' => $client_1,
            'outline' => $outline_1,
            'display_order' => $slipEntryDisplayOrder_1,
            'updated_at' => $slipEntryUpdatedAt_1,
            'deleted' => false,
        ];
        $slips_expected = [
            [
                'slip_id' => $slipId_1,
                'slip' => $convertedSlip_1,
                'entries' => [
                    [
                        'slip_entry_id' => $slipEntryId_1,
                        'slip_entry' => $convertedSlipEntry_1,
                    ],
                ],
            ],
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldReceive('convertExportedTimestamps')
            ->once()
            ->with($slip_1)
            ->andReturn($convertedSlip_1);
        $toolsMock->shouldReceive('convertExportedTimestamps')
            ->once()
            ->with($slipEntry_1)
            ->andReturn($convertedSlipEntry_1);
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('searchBookForExporting')
            ->once()
            ->with($bookId)
            ->andReturn([$slip_1]);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('searchSlipForExporting')
            ->once()
            ->with($slipId_1)
            ->andReturn([$slipEntry_1]);

        $service = new SlipMigrationService($slipMock, $slipEntryMock, $toolsMock);
        $slips_actual = $service->dumpSlips($bookId);

        $this->assertSame($slips_expected, $slips_actual);
    }
}
