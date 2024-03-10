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

class LoadSlipEntriesTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_loads_the_slip_entries(): void
    {
        $bookId = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntryUpdatedAt_1 = '2024-01-28T19:59:30+09:00';
        $slip_1 = [
            'slip_id' => $slipId_1,
        ];
        $slipEntry_1 = [
            'slip_entry_id' => $slipEntryId_1,
            'updated_at' => $slipEntryUpdatedAt_1,
        ];
        $slipEntries = [
            $slipEntryId_1 => ['slip_entry_id' => $slipEntryId_1, 'slip_entry' => $slipEntry_1],
        ];
        $result_expected = [
            [
                $slipEntryId_1 => ['slip_entry_id' => $slipEntryId_1, 'result' => 'created'],
            ],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateSlipEntry')  // call from loadSlipEntry
            ->once()
            ->with($slipEntry_1)
            ->andReturn($slipEntry_1);
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('searchBookForExporting')  // call from exportSlipEntries
            ->once()
            ->with($bookId, $slipId_1)
            ->andReturn([$slip_1]);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('searchSlipForExporting')  // call from exportSlipEntries
            ->once()
            ->with($slipId_1)
            ->andReturn([]);
        $slipEntryMock->shouldReceive('createForImporting')  // call from loadSlipEntry
            ->once()
            ->with($slipEntry_1);

        $service = new SlipMigrationLoaderService($slipMock, $slipEntryMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadSlipEntries($bookId, $slipId_1, $slipEntries);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_slip_to_which_the_entries_should_be_bound_does_not_exist(): void
    {
        $bookId = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntryUpdatedAt_1 = '2024-03-09T19:24:30+09:00';
        $slipEntry_1 = [
            'slip_entry_id' => $slipEntryId_1,
            'updated_at' => $slipEntryUpdatedAt_1,
        ];
        $slipEntries = [
            $slipEntryId_1 => ['slip_entry_id' => $slipEntryId_1, 'slip_entry' => $slipEntry_1],
        ];
        $result_expected = [[], 'The slip that the entires are bound to does not exist. '.$slipId_1];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('searchBookForExporting')  // call from exportSlipEntries
            ->once()
            ->with($bookId, $slipId_1)
            ->andReturn([]);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);

        $service = new SlipMigrationLoaderService($slipMock, $slipEntryMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadSlipEntries($bookId, $slipId_1, $slipEntries);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_one_of_the_entries_does_not_have_its_id(): void
    {
        $bookId = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntryUpdatedAt_1 = '2024-03-09T19:29:30+09:00';
        $slip_1 = [
            'slip_id' => $slipId_1,
        ];
        $slipEntry_1 = [
            'slip_entry_id' => $slipEntryId_1,
            'updated_at' => $slipEntryUpdatedAt_1,
        ];
        $slipEntries = [
            $slipEntryId_1 => ['slip_entry' => $slipEntry_1],
        ];
        $result_expected = [[], 'invalid data format: slip_entry_id'];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('searchBookForExporting')  // call from exportSlipEntries
            ->once()
            ->with($bookId, $slipId_1)
            ->andReturn([$slip_1]);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('searchSlipForExporting')  // call from exportSlipEntries
            ->once()
            ->with($slipId_1)
            ->andReturn([]);

        $service = new SlipMigrationLoaderService($slipMock, $slipEntryMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadSlipEntries($bookId, $slipId_1, $slipEntries);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_one_of_the_entries_is_in_a_invalid_format(): void
    {
        $bookId = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntryUpdatedAt_1 = '2024-03-09T19:47:30+09:00';
        $slip_1 = [
            'slip_id' => $slipId_1,
        ];
        $slipEntry_1 = [
            'updated_at' => $slipEntryUpdatedAt_1,
        ];
        $slipEntries = [
            $slipEntryId_1 => ['slip_entry_id' => $slipEntryId_1, 'slip_entry' => $slipEntry_1],
        ];
        $result_expected = [
            [
                $slipEntryId_1 => ['slip_entry_id' => null, 'result' => null],
            ],
            'invalid data format: slip entry',
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateSlipEntry')  // call from loadSlipEntry
            ->once()
            ->with($slipEntry_1)
            ->andReturn(null);
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('searchBookForExporting')  // call from exportSlipEntries
            ->once()
            ->with($bookId, $slipId_1)
            ->andReturn([$slip_1]);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('searchSlipForExporting')  // call from exportSlipEntries
            ->once()
            ->with($slipId_1)
            ->andReturn([]);

        $service = new SlipMigrationLoaderService($slipMock, $slipEntryMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadSlipEntries($bookId, $slipId_1, $slipEntries);

        $this->assertSame($result_expected, $result_actual);
    }
}
