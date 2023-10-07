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

class SubmitDraftSlipTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_submits_the_draft_slip_for_the_default_book(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 1045;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slipId = (string) Str::uuid();
        $date = '2020-04-03';
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
        $slipMock->shouldReceive('retrieveDraftSlips')
            ->once()
            ->with($bookId)
            ->andReturn([['slip_id' => $slipId]]);
        $slipMock->shouldReceive('updateDateOf')
            ->once()
            ->with($slipId, $date);
        $slipMock->shouldReceive('submitSlip')
            ->once()
            ->with($slipId);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->submitDraftSlip($date);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_specified_book_is_not_readable(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 64;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $date = '2020-04-04';
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, '']);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldNotReceive('retrieveDraftSlips');
        $slipMock->shouldNotReceive('updateDateOf');
        $slipMock->shouldNotReceive('submitSlip');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->submitDraftSlip($date, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_there_is_not_the_draft_slip_to_be_submitted_for_the_specified_book(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 64;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $date = '2020-04-04';
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
        $slipMock->shouldReceive('retrieveDraftSlips')
            ->once()
            ->with($bookId)
            ->andReturn([]);
        $slipMock->shouldNotReceive('updateDateOf');
        $slipMock->shouldNotReceive('submitSlip');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->submitDraftSlip($date, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }
}
