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

class LoadSlipEntryTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_create_the_slip_entry(): void
    {
        $slipId_1 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $debit_1 = (string) Str::uuid();
        $credit_1 = (string) Str::uuid();
        $amount_1 = 270;
        $client_1 = 'client28';
        $outline_1 = 'outline29';
        $slipEntryDisplayOrder_1 = 2;
        $slipEntryUpdatedAt_1 = '2024-03-10T15:13:30+09:00';
        $slipEntryDeleted_1 = false;
        $slipEntry_1 = [
            'slip_entry_id' => $slipEntryId_1,
            'slip_id' => $slipId_1,
            'debit' => $debit_1,
            'credit' => $credit_1,
            'amount' => $amount_1,
            'client' => $client_1,
            'outline' => $outline_1,
            'display_order' => $slipEntryDisplayOrder_1,
            'updated_at' => $slipEntryUpdatedAt_1,
            'deleted' => $slipEntryDeleted_1,
        ];
        $result_expected = [
            ['slip_entry_id' => $slipEntryId_1, 'result' => 'created'],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldNotReceive('isSourceLater');
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateSlipEntry')
            ->once()
            ->with($slipEntry_1)
            ->andReturn($slipEntry_1);
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldNotReceive('updateForImporting');
        $slipEntryMock->shouldReceive('createForImporting')
            ->once()
            ->with($slipEntry_1);

        $service = new SlipMigrationLoaderService($slipMock, $slipEntryMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadSlipEntry($slipEntry_1, []);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_calls_repository_to_update_the_slip_entry(): void
    {
        $slipId_1 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $debit_1 = (string) Str::uuid();
        $credit_1 = (string) Str::uuid();
        $amount_1 = 790;
        $client_1 = 'client80';
        $outline_1 = 'outline81';
        $slipEntryDisplayOrder_1 = 2;
        $slipEntryUpdatedAt_1 = '2024-03-10T15:13:30+09:00';
        $slipEntryDeleted_1 = false;
        $destinationUpdateAt_1 = '2024-03-09T15:14:30+09:00';
        $slipEntry_1 = [
            'slip_entry_id' => $slipEntryId_1,
            'slip_id' => $slipId_1,
            'debit' => $debit_1,
            'credit' => $credit_1,
            'amount' => $amount_1,
            'client' => $client_1,
            'outline' => $outline_1,
            'display_order' => $slipEntryDisplayOrder_1,
            'updated_at' => $slipEntryUpdatedAt_1,
            'deleted' => $slipEntryDeleted_1,
        ];
        $destinationSlipEntries_1 = [
            $slipEntryId_1 => [
                'slip_entry_id' => $slipEntryId_1,
                'updated_at' => $destinationUpdateAt_1,
            ],
        ];
        $result_expected = [
            ['slip_entry_id' => $slipEntryId_1, 'result' => 'updated'],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldReceive('isSourceLater')
            ->once()
            ->with($slipEntryUpdatedAt_1, $destinationUpdateAt_1)
            ->andReturn(true);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateSlipEntry')
            ->once()
            ->with($slipEntry_1)
            ->andReturn($slipEntry_1);
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('updateForImporting')
            ->once()
            ->with($slipEntry_1);
        $slipEntryMock->shouldNotReceive('createForImporting');

        $service = new SlipMigrationLoaderService($slipMock, $slipEntryMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadSlipEntry($slipEntry_1, $destinationSlipEntries_1);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_slip_entry_is_already_up_to_date(): void
    {
        $slipId_1 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $debit_1 = (string) Str::uuid();
        $credit_1 = (string) Str::uuid();
        $amount_1 = 790;
        $client_1 = 'client80';
        $outline_1 = 'outline81';
        $slipEntryDisplayOrder_1 = 2;
        $slipEntryUpdatedAt_1 = '2024-03-09T15:13:30+09:00';
        $slipEntryDeleted_1 = false;
        $destinationUpdateAt_1 = '2024-03-10T15:14:30+09:00';
        $slipEntry_1 = [
            'slip_entry_id' => $slipEntryId_1,
            'slip_id' => $slipId_1,
            'debit' => $debit_1,
            'credit' => $credit_1,
            'amount' => $amount_1,
            'client' => $client_1,
            'outline' => $outline_1,
            'display_order' => $slipEntryDisplayOrder_1,
            'updated_at' => $slipEntryUpdatedAt_1,
            'deleted' => $slipEntryDeleted_1,
        ];
        $destinationSlipEntries_1 = [
            $slipEntryId_1 => [
                'slip_entry_id' => $slipEntryId_1,
                'updated_at' => $destinationUpdateAt_1,
            ],
        ];
        $result_expected = [
            ['slip_entry_id' => $slipEntryId_1, 'result' => 'already up-to-date'],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldReceive('isSourceLater')
            ->once()
            ->with($slipEntryUpdatedAt_1, $destinationUpdateAt_1)
            ->andReturn(false);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateSlipEntry')
            ->once()
            ->with($slipEntry_1)
            ->andReturn($slipEntry_1);
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldNotReceive('updateForImporting');
        $slipEntryMock->shouldNotReceive('createForImporting');

        $service = new SlipMigrationLoaderService($slipMock, $slipEntryMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadSlipEntry($slipEntry_1, $destinationSlipEntries_1);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_slip_entry_is_not_valid(): void
    {
        $result_expected = [
            ['slip_entry_id' => null, 'result' => null],
            'invalid data format: slip entry',
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldNotReceive('isSourceLater');
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateSlipEntry')
            ->once()
            ->with([])
            ->andReturn(null);
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldNotReceive('updateForImporting');
        $slipEntryMock->shouldNotReceive('createForImporting');

        $service = new SlipMigrationLoaderService($slipMock, $slipEntryMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadSlipEntry([], []);

        $this->assertSame($result_expected, $result_actual);
    }
}
