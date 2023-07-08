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
        $accounts = [
            $accountId1 => [],
            $accountId2 => [],
            $accountId3 => [],
            $accountId4 => [],
        ];
        $entries = [
            ['debit' => $accountId1, 'credit' => $accountId2, 'amount' => 4200, 'client' => 'client42', 'outline' => 'outline42'],
            ['debit' => $accountId3, 'credit' => $accountId4, 'amount' => 4300, 'client' => 'client43', 'outline' => 'outline43'],
        ];
        $slipId = (string) Str::uuid();
        $result_expected = [BookKeepingService::STATUS_NORMAL, $slipId];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
            ->once()
            ->with(null, $userId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, $bookId]);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn($accounts);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('createSlipAsDraft')
            ->once()
            ->with($bookId, $outline, $date, $entries, $memo)
            ->andReturn($slipId);
        $slipMock->shouldReceive('submitSlip')
            ->once()
            ->with($slipId);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->createSlip($outline, $date, $entries, $memo);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_specified_book_is_not_writable(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 82;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
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
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN, '']);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldNotReceive('retrieveAccounts');
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldNotReceive('createSlipAsDraft');
        $slipMock->shouldNotReceive('submitSlip');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->createSlip($outline, $date, $entries, $memo, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_debit_in_request_is_invalid_account(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 123;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $outline = 'slip_outline127';
        $date = '2023-07-08';
        $memo = 'memo128';
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $accountId4 = (string) Str::uuid();
        $accounts = [
            $accountId2 => [],
            $accountId3 => [],
            $accountId4 => [],
        ];
        $entries = [
            ['debit' => $accountId1, 'credit' => $accountId2, 'amount' => 860, 'client' => 'client86', 'outline' => 'outline86'],
            ['debit' => $accountId3, 'credit' => $accountId4, 'amount' => 870, 'client' => 'client87', 'outline' => 'outline87'],
        ];
        $slipId = (string) Str::uuid();
        $result_expected = [BookKeepingService::STATUS_ERROR_BAD_CONDITION, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, $bookId]);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn($accounts);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldNotReceive('createSlipAsDraft');
        $slipMock->shouldNotReceive('submitSlip');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->createSlip($outline, $date, $entries, $memo, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_credit_in_request_is_invalid_account(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 172;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $outline = 'slip_outline176';
        $date = '2023-07-08';
        $memo = 'memo78';
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $accountId4 = (string) Str::uuid();
        $accounts = [
            $accountId1 => [],
            $accountId2 => [],
            $accountId3 => [],
        ];
        $entries = [
            ['debit' => $accountId1, 'credit' => $accountId2, 'amount' => 860, 'client' => 'client86', 'outline' => 'outline86'],
            ['debit' => $accountId3, 'credit' => $accountId4, 'amount' => 870, 'client' => 'client87', 'outline' => 'outline87'],
        ];
        $slipId = (string) Str::uuid();
        $result_expected = [BookKeepingService::STATUS_ERROR_BAD_CONDITION, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, $bookId]);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn($accounts);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldNotReceive('createSlipAsDraft');
        $slipMock->shouldNotReceive('submitSlip');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->createSlip($outline, $date, $entries, $memo, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }
}
