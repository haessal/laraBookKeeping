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

class RetrieveDraftSlipsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_retrieves_a_list_of_draft_slips_for_the_default_book(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 434;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accountId_3 = (string) Str::uuid();
        $accountId_4 = (string) Str::uuid();
        $accountId_5 = (string) Str::uuid();
        $accountId_6 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntryId_2 = (string) Str::uuid();
        $slipEntryId_3 = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $slipId_2 = (string) Str::uuid();
        $accounts = [
            $accountId_1 => ['account_title' => 'accountTitle_1'],
            $accountId_2 => ['account_title' => 'accountTitle_2'],
            $accountId_3 => ['account_title' => 'accountTitle_3'],
            $accountId_4 => ['account_title' => 'accountTitle_4'],
            $accountId_5 => ['account_title' => 'accountTitle_5'],
            $accountId_6 => ['account_title' => 'accountTitle_6'],
        ];
        $slips = [
            ['slip_id' => $slipId_1, 'date' => '2019-10-03', 'slip_outline' => 'slipOutline_3', 'slip_memo' => 'slipMemo_3'],
            ['slip_id' => $slipId_2, 'date' => '2019-10-04', 'slip_outline' => 'slipOutline_4', 'slip_memo' => 'slipMemo_4'],
        ];
        $slipEntries_1 = [
            [
                'slip_entry_id' => $slipEntryId_1,
                'slip_id'       => $slipId_1,
                'debit'         => $accountId_1,
                'credit'        => $accountId_2,
                'amount'        => 4670,
                'client'        => 'client_468',
                'outline'       => 'outline_469',
            ],
        ];
        $slipEntries_2 = [
            [
                'slip_entry_id' => $slipEntryId_2,
                'slip_id'       => $slipId_2,
                'debit'         => $accountId_3,
                'credit'        => $accountId_4,
                'amount'        => 4780,
                'client'        => 'client_479',
                'outline'       => 'outline_480',
            ],
            [
                'slip_entry_id' => $slipEntryId_3,
                'slip_id'       => $slipId_2,
                'debit'         => $accountId_5,
                'credit'        => $accountId_6,
                'amount'        => 4870,
                'client'        => 'client_488',
                'outline'       => 'outline_489',
            ],
        ];
        $slips_expected = [
            $slipId_1 => [
                'date'         => '2019-10-03',
                'slip_outline' => 'slipOutline_3',
                'slip_memo'    => 'slipMemo_3',
                'items'        => [
                    $slipEntryId_1 => [
                        'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount'  => 4670,
                        'client'  => 'client_468',
                        'outline' => 'outline_469',
                    ],
                ],
            ],
            $slipId_2 => [
                'date'         => '2019-10-04',
                'slip_outline' => 'slipOutline_4',
                'slip_memo'    => 'slipMemo_4',
                'items'        => [
                    $slipEntryId_2 => [
                        'debit'   => ['account_id' => $accountId_3, 'account_title' => 'accountTitle_3'],
                        'credit'  => ['account_id' => $accountId_4, 'account_title' => 'accountTitle_4'],
                        'amount'  => 4780,
                        'client'  => 'client_479',
                        'outline' => 'outline_480',
                    ],
                    $slipEntryId_3 => [
                        'debit'   => ['account_id' => $accountId_5, 'account_title' => 'accountTitle_5'],
                        'credit'  => ['account_id' => $accountId_6, 'account_title' => 'accountTitle_6'],
                        'amount'  => 4870,
                        'client'  => 'client_488',
                        'outline' => 'outline_489',
                    ],
                ],
            ],
        ];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBook')
            ->once()
            ->with($userId)
            ->andReturn($bookId);
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
        $slipMock->shouldReceive('retrieveDraftSlips')
            ->once()
            ->with($bookId)
            ->andReturn($slips);
        $slipMock->shouldReceive('retrieveSlipEntriesBoundTo')
            ->once()
            ->with($slipId_1)
            ->andReturn($slipEntries_1);
        $slipMock->shouldReceive('retrieveSlipEntriesBoundTo')
            ->once()
            ->with($slipId_2)
            ->andReturn($slipEntries_2);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $slips_actual = $BookKeeping->retrieveDraftSlips();

        $this->assertSame($slips_expected, $slips_actual);
    }

    public function test_it_retrieves_a_list_of_draft_slips_for_the_specified_book(): void
    {
        $bookId = (string) Str::uuid();
        $slips_expected = [];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn([]);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveDraftSlips')
            ->once()
            ->with($bookId)
            ->andReturn([]);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $slips_actual = $BookKeeping->retrieveDraftSlips($bookId);

        $this->assertSame($slips_expected, $slips_actual);
    }
}
