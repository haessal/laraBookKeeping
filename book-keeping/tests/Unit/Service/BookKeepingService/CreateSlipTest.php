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

class CreateSlipTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_creates_a_new_slip_for_the_default_book(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 30;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $outline = 'slip_outline34';
        $date = '2019-01-08';
        $memo = 'memo36';
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $accountId4 = (string) Str::uuid();
        $entries = [
            ['debit' => $accountId1, 'credit' => $accountId2, 'amount' => 4200, 'client' => 'client42', 'outline' => 'outline42'],
            ['debit' => $accountId3, 'credit' => $accountId4, 'amount' => 4300, 'client' => 'client43', 'outline' => 'outline43'],
        ];
        $slipId_expected = (string) Str::uuid();
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBook')
            ->once()
            ->with($userId)
            ->andReturn($bookId);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('createSlipAsDraft')
            ->once()
            ->with($bookId, $outline, $date, $entries, $memo)
            ->andReturn($slipId_expected);
        $slipMock->shouldReceive('submitSlip')
            ->once()
            ->with($slipId_expected);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $slipId_actual = $BookKeeping->createSlip($outline, $date, $entries, $memo);

        $this->assertSame($slipId_expected, $slipId_actual);
    }

    public function test_it_creates_a_new_slip_for_the_specified_book(): void
    {
        $bookId = (string) Str::uuid();
        $outline = 'slip_outline78';
        $date = '2019-01-16';
        $memo = 'memo80';
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $accountId4 = (string) Str::uuid();
        $entries = [
            ['debit' => $accountId1, 'credit' => $accountId2, 'amount' => 860, 'client' => 'client86', 'outline' => 'outline86'],
            ['debit' => $accountId3, 'credit' => $accountId4, 'amount' => 870, 'client' => 'client87', 'outline' => 'outline87'],
        ];
        $slipId_expected = (string) Str::uuid();
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('createSlipAsDraft')
            ->once()
            ->with($bookId, $outline, $date, $entries, $memo)
            ->andReturn($slipId_expected);
        $slipMock->shouldReceive('submitSlip')
            ->once()
            ->with($slipId_expected);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $slipId_actual = $BookKeeping->createSlip($outline, $date, $entries, $memo, $bookId);

        $this->assertSame($slipId_expected, $slipId_actual);
    }
}
