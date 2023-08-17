<?php

namespace Tests\Unit\Service\BookKeepingService;

use App\Service\AccountService;
use App\Service\BookKeepingService;
use App\Service\BookService;
use App\Service\BudgetService;
use App\Service\SlipService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveSlipsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        Carbon::setTestNow();
    }

    public function test_it_retrieves_a_list_of_slips_for_the_default_book_with_their_entries_that_match_the_condition(): void
    {
        $fromDate = '2019-09-01';
        $toDate = '2019-09-30';
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
        $slipEntries = [
            [
                'slip_id'       => $slipId_1,
                'date'          => '2019-09-15',
                'slip_outline'  => 'slipOutline_1',
                'slip_memo'     => 'slipMemo_1',
                'slip_entry_id' => $slipEntryId_1,
                'debit'         => $accountId_1,
                'credit'        => $accountId_2,
                'amount'        => 100,
                'client'        => 'client_1',
                'outline'       => 'outline_1',
            ],
            [
                'slip_id'       => $slipId_2,
                'date'          => '2019-09-25',
                'slip_outline'  => 'slipOutline_2',
                'slip_memo'     => 'slipMemo_2',
                'slip_entry_id' => $slipEntryId_2,
                'debit'         => $accountId_3,
                'credit'        => $accountId_4,
                'amount'        => 2000,
                'client'        => 'client_2',
                'outline'       => 'outline_2',
            ],
            [
                'slip_id'       => $slipId_1,
                'date'          => '2019-09-15',
                'slip_outline'  => 'slipOutline_1',
                'slip_memo'     => 'slipMemo_1',
                'slip_entry_id' => $slipEntryId_3,
                'debit'         => $accountId_5,
                'credit'        => $accountId_6,
                'amount'        => 30000,
                'client'        => 'client_3',
                'outline'       => 'outline_3',
            ],
        ];
        $slips = [
            $slipId_1 => [
                'date'         => '2019-09-15',
                'slip_outline' => 'slipOutline_1',
                'slip_memo'    => 'slipMemo_1',
                'items'        => [
                    $slipEntryId_1 => [
                        'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount'  => 100,
                        'client'  => 'client_1',
                        'outline' => 'outline_1',
                    ],
                    $slipEntryId_3 => [
                        'debit'   => ['account_id' => $accountId_5, 'account_title' => 'accountTitle_5'],
                        'credit'  => ['account_id' => $accountId_6, 'account_title' => 'accountTitle_6'],
                        'amount'  => 30000,
                        'client'  => 'client_3',
                        'outline' => 'outline_3',
                    ],
                ],
            ],
            $slipId_2 => [
                'date'         => '2019-09-25',
                'slip_outline' => 'slipOutline_2',
                'slip_memo'    => 'slipMemo_2',
                'items'        => [
                    $slipEntryId_2 => [
                        'debit'   => ['account_id' => $accountId_3, 'account_title' => 'accountTitle_3'],
                        'credit'  => ['account_id' => $accountId_4, 'account_title' => 'accountTitle_4'],
                        'amount'  => 2000,
                        'client'  => 'client_2',
                        'outline' => 'outline_2',
                    ],
                ],
            ],
        ];
        $condition = ['debit' => null, 'credit' => null, 'and_or' => null, 'keyword' => null];
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
        $slipMock->shouldReceive('retrieveSlipEntries')
            ->once()
            ->with($fromDate, $toDate, $condition, $bookId)
            ->andReturn($slipEntries);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->retrieveSlips($fromDate, $toDate, null, null, null, null);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_retrieves_a_list_of_slips_for_the_specified_book_with_their_entries_that_match_the_condition(): void
    {
        $fromDate = '2019-10-01';
        $toDate = '2019-10-31';
        $bookId = (string) Str::uuid();
        $userId = 160;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slips = [];
        $condition = ['debit' => null, 'credit' => null, 'and_or' => null, 'keyword' => null];
        $result_expected = [BookKeepingService::STATUS_NORMAL, $slips];
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
            ->andReturn([]);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveSlipEntries')
            ->once()
            ->with($fromDate, $toDate, $condition, $bookId)
            ->andReturn($slips);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->retrieveSlips($fromDate, $toDate, null, null, null, null, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_retrieves_a_list_of_slips_for_the_specified_book_with_their_entries(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 196;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slips = [];
        $result_expected = [BookKeepingService::STATUS_NORMAL, $slips];
        $condition = ['debit' => null, 'credit' => null, 'and_or' => null, 'keyword' => null];
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
            ->andReturn([]);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveSlipEntries')
            ->once()
            ->with(BookKeepingService::ORIGIN_DATE, '2019-10-10', $condition, $bookId)
            ->andReturn($slips);
        Carbon::setTestNow(new Carbon('2019-10-10 09:59:59'));

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->retrieveSlips(null, null, null, null, null, null, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_specified_book_is_not_readable(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 236;
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
        $slipMock->shouldNotReceive('retrieveSlipEntries');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->retrieveSlips(null, null, null, null, null, null, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }
}
