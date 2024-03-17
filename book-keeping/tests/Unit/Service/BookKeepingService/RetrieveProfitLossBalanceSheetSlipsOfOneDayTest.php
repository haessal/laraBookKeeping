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

class RetrieveProfitLossBalanceSheetSlipsOfOneDayTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_retrieves_the_statement_of_the_default_book(): void
    {
        $date = '2019-10-31';
        $bookId = (string) Str::uuid();
        $userId = 11;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accountId_3 = (string) Str::uuid();
        $accountId_4 = (string) Str::uuid();
        $accountId_5 = (string) Str::uuid();
        $accountId_6 = (string) Str::uuid();
        $accountId_7 = (string) Str::uuid();
        $accountId_8 = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountGroupId_2 = (string) Str::uuid();
        $accountGroupId_3 = (string) Str::uuid();
        $accountGroupId_4 = (string) Str::uuid();
        $accountGroupId_5 = (string) Str::uuid();
        $accountGroupId_6 = (string) Str::uuid();
        $accounts = [
            $accountId_1 => [
                'account_type' => AccountService::ACCOUNT_TYPE_ASSET,
                'account_group_id' => $accountGroupId_1,
                'account_group_title' => 'accountGroupTitle_1',
                'is_current' => 0,
                'account_bk_code' => 1201,
                'account_title' => 'accountTitle_1',
                'account_group_bk_code' => 1200,
                'created_at' => '2019-12-01 12:00:01',
                'account_group_created_at' => '2019-12-01 12:00:00',
            ],
            $accountId_2 => [
                'account_type' => AccountService::ACCOUNT_TYPE_ASSET,
                'account_group_id' => $accountGroupId_1,
                'account_group_title' => 'accountGroupTitle_1',
                'is_current' => 0,
                'account_bk_code' => 1202,
                'account_title' => 'accountTitle_2',
                'account_group_bk_code' => 1200,
                'created_at' => '2019-12-01 12:00:02',
                'account_group_created_at' => '2019-12-01 12:00:00',
            ],
            $accountId_3 => [
                'account_type' => AccountService::ACCOUNT_TYPE_LIABILITY,
                'account_group_id' => $accountGroupId_2,
                'account_group_title' => 'accountGroupTitle_2',
                'is_current' => 0,
                'account_bk_code' => 2303,
                'account_title' => 'accountTitle_3',
                'account_group_bk_code' => 2300,
                'created_at' => '2019-12-01 12:23:03',
                'account_group_created_at' => '2019-12-01 12:23:00',
            ],
            $accountId_4 => [
                'account_type' => AccountService::ACCOUNT_TYPE_EXPENSE,
                'account_group_id' => $accountGroupId_3,
                'account_group_title' => 'accountGroupTitle_3',
                'is_current' => 0,
                'account_bk_code' => 4104,
                'account_title' => 'accountTitle_4',
                'account_group_bk_code' => 4100,
                'created_at' => '2019-12-01 12:41:04',
                'account_group_created_at' => '2019-12-01 12:41:00',
            ],
            $accountId_5 => [
                'account_type' => AccountService::ACCOUNT_TYPE_REVENUE,
                'account_group_id' => $accountGroupId_4,
                'account_group_title' => 'accountGroupTitle_4',
                'is_current' => 0,
                'account_bk_code' => 5105,
                'account_title' => 'accountTitle_5',
                'account_group_bk_code' => 5100,
                'created_at' => '2019-12-01 12:51:05',
                'account_group_created_at' => '2019-12-01 12:51:00',
            ],
            $accountId_6 => [
                'account_type' => AccountService::ACCOUNT_TYPE_ASSET,
                'account_group_id' => $accountGroupId_5,
                'account_group_title' => 'accountGroupTitle_5',
                'is_current' => 1,
                'account_bk_code' => 1106,
                'account_title' => 'accountTitle_6',
                'account_group_bk_code' => 1100,
                'created_at' => '2019-12-01 12:11:06',
                'account_group_created_at' => '2019-12-01 12:11:00',
            ],
            $accountId_7 => [
                'account_type' => AccountService::ACCOUNT_TYPE_LIABILITY,
                'account_group_id' => $accountGroupId_6,
                'account_group_title' => 'accountGroupTitle_6',
                'is_current' => 1,
                'account_bk_code' => 2207,
                'account_title' => 'accountTitle_7',
                'account_group_bk_code' => 2200,
                'created_at' => '2019-12-01 12:22:07',
                'account_group_created_at' => '2019-12-01 12:22:00',
            ],
            $accountId_8 => [
                'account_type' => AccountService::ACCOUNT_TYPE_EXPENSE,
                'account_group_id' => $accountGroupId_3,
                'account_group_title' => 'accountGroupTitle_3',
                'is_current' => 0,
                'account_bk_code' => 4108,
                'account_title' => 'accountTitle_8',
                'account_group_bk_code' => 4100,
                'created_at' => '2019-12-01 12:41:08',
                'account_group_created_at' => '2019-12-01 12:41:00',
            ],
        ];
        $amountFlows = [
            $accountId_1 => ['debit' => 100, 'credit' => 100],
            $accountId_2 => ['debit' => 2100, 'credit' => 2000],
            $accountId_3 => ['debit' => 3000, 'credit' => 3200],
            $accountId_4 => ['debit' => 4000, 'credit' => 4300],
            $accountId_5 => ['debit' => 5400, 'credit' => 5000],
            $accountId_6 => ['debit' => 6500, 'credit' => 0],
            $accountId_7 => ['debit' => 0, 'credit' => 7600],
            $accountId_8 => ['debit' => 8700, 'credit' => 8000],
        ];
        $slipId_1 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $slipOutline = 'slip_outline150';
        $slipMemo = 'slip_memo151';
        $amount = 2590;
        $client = 'client156';
        $outline = 'outline157';
        $slipEntries = [[
            'slip_id' => $slipId_1,
            'date' => $date,
            'slip_outline' => $slipOutline,
            'slip_memo' => $slipMemo,
            'slip_entry_id' => $slipEntryId_1,
            'debit' => $accountId_1,
            'credit' => $accountId_2,
            'amount' => $amount,
            'client' => $client,
            'outline' => $outline,
        ]];
        $profitLoss = [
            AccountService::ACCOUNT_TYPE_EXPENSE => ['amount' => 400, 'groups' => [
                $accountGroupId_3 => [
                    'title' => 'accountGroupTitle_3',
                    'isCurrent' => 0,
                    'amount' => 400,
                    'bk_code' => 4100,
                    'createdAt' => '2019-12-01 12:41:00',
                    'items' => [
                        $accountId_4 => [
                            'title' => 'accountTitle_4',
                            'amount' => -300,
                            'bk_code' => 4104,
                            'createdAt' => '2019-12-01 12:41:04',
                        ],
                        $accountId_8 => [
                            'title' => 'accountTitle_8',
                            'amount' => 700,
                            'bk_code' => 4108,
                            'createdAt' => '2019-12-01 12:41:08',
                        ],
                    ],
                ],
            ]],
            AccountService::ACCOUNT_TYPE_REVENUE => ['amount' => -400, 'groups' => [
                $accountGroupId_4 => [
                    'title' => 'accountGroupTitle_4',
                    'isCurrent' => 0,
                    'amount' => -400,
                    'bk_code' => 5100,
                    'createdAt' => '2019-12-01 12:51:00',
                    'items' => [
                        $accountId_5 => [
                            'title' => 'accountTitle_5',
                            'amount' => -400,
                            'bk_code' => 5105,
                            'createdAt' => '2019-12-01 12:51:05',
                        ],
                    ],
                ],
            ]],
            'net_income' => ['amount' => -800],
        ];
        $balanceSheet = [
            AccountService::ACCOUNT_TYPE_ASSET => ['amount' => 6600, 'groups' => [
                $accountGroupId_1 => [
                    'title' => 'accountGroupTitle_1',
                    'isCurrent' => 0,
                    'amount' => 100,
                    'bk_code' => 1200,
                    'createdAt' => '2019-12-01 12:00:00',
                    'items' => [
                        $accountId_2 => [
                            'title' => 'accountTitle_2',
                            'amount' => 100,
                            'bk_code' => 1202,
                            'createdAt' => '2019-12-01 12:00:02',
                        ],
                    ],
                ],
                $accountGroupId_5 => [
                    'title' => 'accountGroupTitle_5',
                    'isCurrent' => 1,
                    'amount' => 6500,
                    'bk_code' => 1100,
                    'createdAt' => '2019-12-01 12:11:00',
                    'items' => [
                        $accountId_6 => [
                            'title' => 'accountTitle_6',
                            'amount' => 6500,
                            'bk_code' => 1106,
                            'createdAt' => '2019-12-01 12:11:06',
                        ],
                    ],
                ],
            ]],
            AccountService::ACCOUNT_TYPE_LIABILITY => ['amount' => 7800, 'groups' => [
                $accountGroupId_2 => [
                    'title' => 'accountGroupTitle_2',
                    'isCurrent' => 0,
                    'amount' => 200,
                    'bk_code' => 2300,
                    'createdAt' => '2019-12-01 12:23:00',
                    'items' => [
                        $accountId_3 => [
                            'title' => 'accountTitle_3',
                            'amount' => 200,
                            'bk_code' => 2303,
                            'createdAt' => '2019-12-01 12:23:03',
                        ],
                    ],
                ],
                $accountGroupId_6 => [
                    'title' => 'accountGroupTitle_6',
                    'isCurrent' => 1,
                    'amount' => 7600,
                    'bk_code' => 2200,
                    'createdAt' => '2019-12-01 12:22:00',
                    'items' => [
                        $accountId_7 => [
                            'title' => 'accountTitle_7',
                            'amount' => 7600,
                            'bk_code' => 2207,
                            'createdAt' => '2019-12-01 12:22:07',
                        ],
                    ],
                ],
            ]],
            'current_net_asset' => ['amount' => -1100],
            'net_asset' => ['amount' => -1200],
        ];
        $slips = [$slipId_1 => [
            'date' => $date,
            'slip_outline' => $slipOutline,
            'slip_memo' => $slipMemo,
            'items' => [$slipEntryId_1 => [
                'debit' => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                'credit' => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                'amount' => $amount,
                'client' => $client,
                'outline' => $outline,
            ]],
        ]];
        $result_expected = [BookKeepingService::STATUS_NORMAL, [
            'profit_loss' => $profitLoss,
            'balance_sheet' => $balanceSheet,
            'slips' => $slips,
        ]];
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
        $slipMock->shouldReceive('retrieveAmountFlows')
            ->once()
            ->with($date, $date, $bookId)
            ->andReturn($amountFlows);
        $slipMock->shouldReceive('retrieveAmountFlows')
            ->once()
            ->with('1970-01-01', $date, $bookId)
            ->andReturn($amountFlows);
        $slipMock->shouldReceive('retrieveSlipEntries')
            ->once()
            ->with($date, $date, ['debit' => null, 'credit' => null, 'and_or' => null, 'keyword' => null], $bookId)
            ->andReturn($slipEntries);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->retrieveProfitLossBalanceSheetSlipsOfOneDay($date);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_specified_book_is_not_readable(): void
    {
        $date = '2019-10-31';
        $bookId = (string) Str::uuid();
        $userId = 286;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
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
        $slipMock->shouldNotReceive('retrieveAmountFlows');
        $slipMock->shouldNotReceive('retrieveSlipEntries');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->retrieveProfitLossBalanceSheetSlipsOfOneDay($date, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }
}
