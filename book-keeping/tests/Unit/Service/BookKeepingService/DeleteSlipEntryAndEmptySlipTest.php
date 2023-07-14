<?php

namespace Tests\Unit\Service\BookKeepingService;

use App\Service\AccountService;
use App\Service\BookKeepingService;
use App\Service\BookService;
use App\Service\BudgetService;
use App\Service\SlipService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class DeleteSlipEntryAndEmptySlipTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_deletes_the_slip_entry_and_leaves_the_slip_because_the_entry_is_not_the_last_one(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 255;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slipId = (string) Str::uuid();
        $slipEntryId = (string) Str::uuid();
        $slipEntryId_r = (string) Str::uuid();
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $accountId4 = (string) Str::uuid();
        $result_expected = [BookKeepingService::STATUS_NORMAL, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
            ->once()
            ->with(null, $userId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, $bookId]);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveSlipEntry')
            ->once()
            ->with($slipEntryId, $bookId, true)
            ->andReturn([
                'slip_id'       => $slipId,
                'date'          => '2020-04-30',
                'slip_outline'  => 'slip_outline284',
                'slip_memo'     => null,
                'slip_entry_id' => $slipEntryId,
                'debit'         => $accountId1,
                'credit'        => $accountId2,
                'amount'        => 289,
                'client'        => 'client290',
                'outline'       => 'outline291',
            ]);
        $slipMock->shouldReceive('deleteSlipEntry')
            ->once()
            ->with($slipEntryId);
        $slipMock->shouldReceive('retrieveSlipEntriesBoundTo')
            ->once()
            ->with($slipId)
            ->andReturn([
                [
                    'slip_entry_id' => $slipEntryId_r,
                    'slip_id'       => $slipId,
                    'debit'         => $accountId3,
                    'credit'        => $accountId4,
                    'amount'        => 1340,
                    'client'        => 'client135',
                    'outline'       => 'outline136',
                ],
            ]);
        $slipMock->shouldNotReceive('deleteSlip');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->deleteSlipEntryAndEmptySlip($slipEntryId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_deletes_the_slip_entry_and_also_deletes_the_slip_because_the_entry_is_the_last_one(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 91;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slipId = (string) Str::uuid();
        $slipEntryId = (string) Str::uuid();
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $result_expected = [BookKeepingService::STATUS_NORMAL, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, $bookId]);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveSlipEntry')
            ->once()
            ->with($slipEntryId, $bookId, true)
            ->andReturn([
                'slip_id'       => $slipId,
                'date'          => '2020-04-30',
                'slip_outline'  => 'slip_outline284',
                'slip_memo'     => null,
                'slip_entry_id' => $slipEntryId,
                'debit'         => $accountId1,
                'credit'        => $accountId2,
                'amount'        => 289,
                'client'        => 'client290',
                'outline'       => 'outline291',
            ]);
        $slipMock->shouldReceive('deleteSlipEntry')
            ->once()
            ->with($slipEntryId);
        $slipMock->shouldReceive('retrieveSlipEntriesBoundTo')
            ->once()
            ->with($slipId)
            ->andReturn([]);
        $slipMock->shouldReceive('deleteSlip')
            ->once()
            ->with($slipId);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->deleteSlipEntryAndEmptySlip($slipEntryId, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_slip_entry_to_be_deleted_is_not_found(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 146;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slipEntryId = (string) Str::uuid();
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, $bookId]);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveSlipEntry')
            ->once()
            ->with($slipEntryId, $bookId, true)
            ->andReturn(null);
        $slipMock->shouldNotReceive('deleteSlipEntry');
        $slipMock->shouldNotReceive('retrieveSlipEntriesBoundTo');
        $slipMock->shouldNotReceive('deleteSlip');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->deleteSlipEntryAndEmptySlip($slipEntryId, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_specified_book_is_not_writable(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 186;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slipEntryId = (string) Str::uuid();
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN, $bookId]);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldNotReceive('retrieveSlipEntry');
        $slipMock->shouldNotReceive('deleteSlipEntry');
        $slipMock->shouldNotReceive('retrieveSlipEntriesBoundTo');
        $slipMock->shouldNotReceive('deleteSlip');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->deleteSlipEntryAndEmptySlip($slipEntryId, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }
}
