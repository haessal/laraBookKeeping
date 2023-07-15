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

class RetrieveSlipTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_retrieves_the_slip_for_the_default_book(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 1191;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slipId = (string) Str::uuid();
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accountId_3 = (string) Str::uuid();
        $accountId_4 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntryId_2 = (string) Str::uuid();
        $date = '2020-04-30';
        $slip_outline = 'slipOutline_1';
        $slip_memo = 'slipMemo_1';
        $slip_head = ['book_id' => $bookId, 'slip_id' => $slipId, 'date' => $date, 'slip_outline' => $slip_outline, 'slip_memo' => $slip_memo];
        $accounts = [
            $accountId_1 => ['account_title' => 'accountTitle_1'],
            $accountId_2 => ['account_title' => 'accountTitle_2'],
            $accountId_3 => ['account_title' => 'accountTitle_3'],
            $accountId_4 => ['account_title' => 'accountTitle_4'],
        ];
        $slipEntries = [
            [
                'slip_entry_id' => $slipEntryId_1,
                'slip_id'       => $slipId,
                'debit'         => $accountId_1,
                'credit'        => $accountId_2,
                'amount'        => 100,
                'client'        => 'client_1',
                'outline'       => 'outline_1',
            ],
            [
                'slip_entry_id' => $slipEntryId_2,
                'slip_id'       => $slipId,
                'debit'         => $accountId_3,
                'credit'        => $accountId_4,
                'amount'        => 30000,
                'client'        => 'client_2',
                'outline'       => 'outline_2',
            ],
        ];
        $slips = [
            $slipId => [
                'date'         => $date,
                'slip_outline' => $slip_outline,
                'slip_memo'    => $slip_memo,
                'items'        => [
                    $slipEntryId_1 => [
                        'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount'  => 100,
                        'client'  => 'client_1',
                        'outline' => 'outline_1',
                    ],
                    $slipEntryId_2 => [
                        'debit'   => ['account_id' => $accountId_3, 'account_title' => 'accountTitle_3'],
                        'credit'  => ['account_id' => $accountId_4, 'account_title' => 'accountTitle_4'],
                        'amount'  => 30000,
                        'client'  => 'client_2',
                        'outline' => 'outline_2',
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
        $slipMock->shouldReceive('retrieveSlip')
            ->once()
            ->with($slipId, $bookId)
            ->andReturn($slip_head);
        $slipMock->shouldReceive('retrieveSlipEntriesBoundTo')
            ->once()
            ->with($slipId)
            ->andReturn($slipEntries);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->retrieveSlip($slipId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_specified_book_is_not_readable(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 124;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slipId = (string) Str::uuid();
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
        $slipMock->shouldNotReceive('retrieveSlip');
        $slipMock->shouldNotReceive('retrieveSlipEntriesBoundTo');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->retrieveSlip($slipId, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_retrieves_nothing_because_the_slip_for_the_specified_book_is_not_found(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 155;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slipId = (string) Str::uuid();
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckReadable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, $bookId]);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldNotReceive('retrieveAccounts');
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveSlip')
            ->once()
            ->with($slipId, $bookId)
            ->andReturn(null);
        $slipMock->shouldNotReceive('retrieveSlipEntriesBoundTo');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->retrieveSlip($slipId, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }
}
