<?php

namespace Tests\Unit\Service\SlipMigrationLoaderService;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\DataProvider\SlipRepositoryInterface;
use App\Service\BookKeepingMigrationTools;
use App\Service\BookKeepingMigrationValidator;
use App\Service\SlipMigrationLoaderService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class LoadSlipsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_loads_the_slips(): void
    {
        $bookId = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $slipUpdatedAt_1 = '2024-03-10T16:00:26+09:00';
        $slipEntryUpdatedAt_1 = '2024-03-10T16:00:25+09:00';
        $slip_1 = [
            'slip_id' => $slipId_1,
            'updated_at' => $slipUpdatedAt_1,
        ];
        $slipEntry_1 = [
            'slip_entry_id' => $slipEntryId_1,
            'updated_at' => $slipEntryUpdatedAt_1,
        ];
        $slips = [
            $slipId_1 => [
                'slip_id' => $slipId_1,
                'slip' => $slip_1,
                'entries' => [
                    $slipEntryId_1 => [
                        'slip_entry_id' => $slipEntryId_1,
                        'slip_entry' => $slipEntry_1,
                    ],
                ],
            ],
        ];
        $result_expected = [
            [
                $slipId_1 => [
                    'slip_id' => $slipId_1,
                    'result' => 'created',
                    'entries' => [
                        $slipEntryId_1 => [
                            'slip_entry_id' => $slipEntryId_1,
                            'result' => 'already up-to-date',
                        ],
                    ],
                ],
            ],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldReceive('isSourceLater')  // call from loadSlipEntry from loadSlipEntries
            ->once()
            ->with($slipEntryUpdatedAt_1, $slipEntryUpdatedAt_1)
            ->andReturn(false);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateSlip')  // call from loadSlip
            ->once()
            ->with($slip_1)
            ->andReturn($slip_1);
        $validatorMock->shouldReceive('validateSlipEntry')  // call from loadSlipEntry from loadSlipEntries
            ->once()
            ->with($slipEntry_1)
            ->andReturn($slipEntry_1);
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('searchBookForExporting')  // call from exportSlips
            ->once()
            ->with($bookId)
            ->andReturn([]);
        $slipMock->shouldReceive('searchBookForExporting')  // call from exportSlipEntries from loadSlipEntries
            ->once()
            ->with($bookId, $slipId_1)
            ->andReturn($slips);
        $slipMock->shouldReceive('createForImporting')  // call from loadSlip
            ->once()
            ->with($slip_1);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('searchSlipForExporting')  // call from exportSlipEntries from loadSlipEntries
            ->once()
            ->with($slipId_1)
            ->andReturn([$slipEntry_1]);

        $service = new SlipMigrationLoaderService($slipMock, $slipEntryMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadSlips($bookId, $slips);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_one_of_the_slips_does_not_have_its_id(): void
    {
        $bookId = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $slips = [
            $slipId_1 => [],
        ];
        $result_expected = [[], 'invalid data format: slip_id'];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('searchBookForExporting')  // call from exportSlips
            ->once()
            ->with($bookId)
            ->andReturn([]);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);

        $service = new SlipMigrationLoaderService($slipMock, $slipEntryMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadSlips($bookId, $slips);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_one_of_the_slips_is_in_a_invalid_format(): void
    {
        $bookId = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $slipUpdatedAt_1 = '2024-03-10T16:01:36+09:00';
        $slipEntryUpdatedAt_1 = '2024-03-10T16:01:37+09:00';
        $slip_1 = [
            'updated_at' => $slipUpdatedAt_1,
        ];
        $slipEntry_1 = [
            'slip_entry_id' => $slipEntryId_1,
            'updated_at' => $slipEntryUpdatedAt_1,
        ];
        $slips = [
            $slipId_1 => [
                'slip_id' => $slipId_1,
                'slip' => $slip_1,
                'entries' => [
                    $slipEntryId_1 => [
                        'slip_entry_id' => $slipEntryId_1,
                        'slip_entry' => $slipEntry_1,
                    ],
                ],
            ],
        ];
        $result_expected = [
            [
                $slipId_1 => [
                    'slip_id' => null,
                    'result' => null,
                ],
            ],
            'invalid data format: slip',
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateSlip')  // call from loadSlip
            ->once()
            ->with($slip_1)
            ->andReturn(null);
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('searchBookForExporting')  // call from exportSlips
            ->once()
            ->with($bookId)
            ->andReturn([]);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);

        $service = new SlipMigrationLoaderService($slipMock, $slipEntryMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadSlips($bookId, $slips);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_can_not_load_the_slip_entries_because_the_entries_is_in_a_invalid_format(): void
    {
        $bookId = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $slipUpdatedAt_1 = '2024-03-10T16:10:34+09:00';
        $slip_1 = [
            'slip_id' => $slipId_1,
            'updated_at' => $slipUpdatedAt_1,
        ];
        $slips = [
            $slipId_1 => [
                'slip_id' => $slipId_1,
                'slip' => $slip_1,
                'entries' => $slipEntryId_1,
            ],
        ];
        $result_expected = [
            [
                $slipId_1 => [
                    'slip_id' => $slipId_1,
                    'result' => 'created',
                ],
            ],
            'invalid data format: slip entries',
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateSlip')  // call from loadSlip
            ->once()
            ->with($slip_1)
            ->andReturn($slip_1);
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('searchBookForExporting')  // call from exportSlips
            ->once()
            ->with($bookId)
            ->andReturn([]);
        $slipMock->shouldReceive('createForImporting')  // call from loadSlip
            ->once()
            ->with($slip_1);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);

        $service = new SlipMigrationLoaderService($slipMock, $slipEntryMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadSlips($bookId, $slips);

        $this->assertSame($result_expected, $result_actual);
    }
}
