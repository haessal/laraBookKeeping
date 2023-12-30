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

class RetrieveSlipEntryTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_retrieves_the_slip_entry_for_the_default_book(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 12;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slipEntryId = (string) Str::uuid();
        $slipId = (string) Str::uuid();
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accounts = [
            $accountId_1 => ['account_title' => 'accountTitle_1'],
            $accountId_2 => ['account_title' => 'accountTitle_2'],
        ];
        $date = '2020-03-31';
        $slip_outline = 'slip_outline1325';
        $slip_memo = 'slip_memo1326';
        $amount = 1327;
        $client = 'client1328';
        $outline = 'outline1329';
        $slipEntry = [
            'slip_id' => $slipId,
            'date' => $date,
            'slip_outline' => $slip_outline,
            'slip_memo' => $slip_memo,
            'slip_entry_id' => $slipEntryId,
            'debit' => $accountId_1,
            'credit' => $accountId_2,
            'amount' => $amount,
            'client' => $client,
            'outline' => $outline,
        ];
        $slips = [
            $slipId => [
                'date' => $date,
                'slip_outline' => $slip_outline,
                'slip_memo' => $slip_memo,
                'items' => [
                    $slipEntryId => [
                        'debit' => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit' => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount' => $amount,
                        'client' => $client,
                        'outline' => $outline,
                    ],
                ],
            ],
        ];
        $result_expected = [BookKeepingService::STATUS_NORMAL, $slips];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckReadable')
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
        $slipMock->shouldReceive('retrieveSlipEntry')
            ->once()
            ->with($slipEntryId, $bookId, false)
            ->andReturn($slipEntry);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $slips_actual = $BookKeeping->retrieveSlipEntry($slipEntryId);

        $this->assertSame($result_expected, $slips_actual);
    }

    public function test_it_does_nothing_because_the_specified_book_is_not_readable(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 102;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slipEntryId = (string) Str::uuid();
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckReadable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, '']);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldNotReceive('retrieveAccounts');
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldNotReceive('retrieveSlipEntry');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->retrieveSlipEntry($slipEntryId, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_retrieves_nothing_because_the_slip_entry_for_the_specified_book_is_not_found(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 132;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slipEntryId = (string) Str::uuid();
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accounts = [
            $accountId_1 => ['account_title' => 'accountTitle_1'],
            $accountId_2 => ['account_title' => 'accountTitle_2'],
        ];
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckReadable')
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
        $slipMock->shouldReceive('retrieveSlipEntry')
            ->once()
            ->with($slipEntryId, $bookId, false)
            ->andReturn(null);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->retrieveSlipEntry($slipEntryId, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }
}
