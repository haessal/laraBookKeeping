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

class LoadSlipTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_create_the_slip(): void
    {
        $bookId = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $slipOutline_1 = 'outline25';
        $slipMemo_1 = 'memo26';
        $slipDate_1 = '2024-03-09';
        $isDraft_1 = false;
        $slipDisplayOrder_1 = 3;
        $slipUpdatedAt_1 = '2024-03-09T18:27:31+09:00';
        $slipDeleted_1 = false;
        $slip_1 = [
            'slip_id' => $slipId_1,
            'book_id' => $bookId,
            'slip_outline' => $slipOutline_1,
            'slip_memo' => $slipMemo_1,
            'date' => $slipDate_1,
            'is_draft' => $isDraft_1,
            'display_order' => $slipDisplayOrder_1,
            'updated_at' => $slipUpdatedAt_1,
            'deleted' => $slipDeleted_1,
        ];
        $result_expected = [
            ['slip_id' => $slipId_1, 'result' => 'created'],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldNotReceive('isSourceLater');
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateSlip')
            ->once()
            ->with($slip_1)
            ->andReturn($slip_1);
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldNotReceive('updateForImporting');
        $slipMock->shouldReceive('createForImporting')
            ->once()
            ->with($slip_1);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);

        $service = new SlipMigrationLoaderService($slipMock, $slipEntryMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadSlip($slip_1, []);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_calls_repository_to_update_the_slip(): void
    {
        $bookId = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $slipOutline_1 = 'outline75';
        $slipMemo_1 = 'memo76';
        $slipDate_1 = '2024-03-09';
        $isDraft_1 = false;
        $slipDisplayOrder_1 = 3;
        $slipUpdatedAt_1 = '2024-03-09T18:27:31+09:00';
        $slipDeleted_1 = false;
        $slip_1 = [
            'slip_id' => $slipId_1,
            'book_id' => $bookId,
            'slip_outline' => $slipOutline_1,
            'slip_memo' => $slipMemo_1,
            'date' => $slipDate_1,
            'is_draft' => $isDraft_1,
            'display_order' => $slipDisplayOrder_1,
            'updated_at' => $slipUpdatedAt_1,
            'deleted' => $slipDeleted_1,
        ];
        $destinationUpdateAt_1 = '2024-03-08T18:27:31+09:00';
        $destinationSlips_1 = [
            $slipId_1 => [
                'slip_id' => $slipId_1,
                'updated_at' => $destinationUpdateAt_1,
            ],
        ];
        $result_expected = [
            ['slip_id' => $slipId_1, 'result' => 'updated'],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldReceive('isSourceLater')
            ->once()
            ->with($slipUpdatedAt_1, $destinationUpdateAt_1)
            ->andReturn(true);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateSlip')
            ->once()
            ->with($slip_1)
            ->andReturn($slip_1);
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('updateForImporting')
            ->once()
            ->with($slip_1);
        $slipMock->shouldNotReceive('createForImporting');
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);

        $service = new SlipMigrationLoaderService($slipMock, $slipEntryMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadSlip($slip_1, $destinationSlips_1);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_slip_is_already_up_to_date(): void
    {
        $bookId = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $slipOutline_1 = 'outline135';
        $slipMemo_1 = 'memo136';
        $slipDate_1 = '2024-03-09';
        $isDraft_1 = false;
        $slipDisplayOrder_1 = 3;
        $slipUpdatedAt_1 = '2024-03-09T18:27:31+09:00';
        $slipDeleted_1 = false;
        $slip_1 = [
            'slip_id' => $slipId_1,
            'book_id' => $bookId,
            'slip_outline' => $slipOutline_1,
            'slip_memo' => $slipMemo_1,
            'date' => $slipDate_1,
            'is_draft' => $isDraft_1,
            'display_order' => $slipDisplayOrder_1,
            'updated_at' => $slipUpdatedAt_1,
            'deleted' => $slipDeleted_1,
        ];
        $destinationUpdateAt_1 = '2024-03-09T18:40:31+09:00';
        $destinationSlips_1 = [
            $slipId_1 => [
                'slip_id' => $slipId_1,
                'updated_at' => $destinationUpdateAt_1,
            ],
        ];
        $result_expected = [
            ['slip_id' => $slipId_1, 'result' => 'already up-to-date'],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldReceive('isSourceLater')
            ->once()
            ->with($slipUpdatedAt_1, $destinationUpdateAt_1)
            ->andReturn(false);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateSlip')
            ->once()
            ->with($slip_1)
            ->andReturn($slip_1);
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldNotReceive('updateForImporting');
        $slipMock->shouldNotReceive('createForImporting');
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);

        $service = new SlipMigrationLoaderService($slipMock, $slipEntryMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadSlip($slip_1, $destinationSlips_1);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_slip_is_not_valid(): void
    {
        $result_expected = [
            ['slip_id' => null, 'result' => null],
            'invalid data format: slip',
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldNotReceive('isSourceLater');
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateSlip')
            ->once()
            ->with([])
            ->andReturn(null);
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldNotReceive('updateForImporting');
        $slipMock->shouldNotReceive('createForImporting');
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);

        $service = new SlipMigrationLoaderService($slipMock, $slipEntryMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadSlip([], []);

        $this->assertSame($result_expected, $result_actual);
    }
}
