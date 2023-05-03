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

class RetrieveAccountsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_retrieves_a_list_of_accounts_for_the_default_book(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 192;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $accountId_1 = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accounts = [
            $accountId_1 => [
                'account_type'             => AccountService::ACCOUNT_TYPE_ASSET,
                'account_group_id'         => $accountGroupId_1,
                'account_group_title'      => 'accountGroupTitle_1',
                'is_current'               => 0,
                'account_id'               => $accountId_1,
                'account_title'            => 'accountTitle_1',
                'description'              => 'description_1',
                'selectable'               => 1,
                'account_bk_code'          => 1201,
                'created_at'               => '2019-12-03 12:00:01',
                'account_group_bk_code'    => 1200,
                'account_group_created_at' => '2019-12-04 12:00:12',
            ],
        ];
        $accounts_expected = $accounts;
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

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $accounts_actual = $BookKeeping->retrieveAccounts();

        $this->assertSame($accounts_expected, $accounts_actual);
    }

    public function test_it_retrieves_a_list_of_accounts_for_the_specified_book(): void
    {
        $bookId = (string) Str::uuid();
        $accountId_1 = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accounts = [
            $accountId_1 => [
                'account_type'             => AccountService::ACCOUNT_TYPE_ASSET,
                'account_group_id'         => $accountGroupId_1,
                'account_group_title'      => 'accountGroupTitle_1',
                'is_current'               => 0,
                'account_id'               => $accountId_1,
                'account_title'            => 'accountTitle_1',
                'description'              => 'description_1',
                'selectable'               => 1,
                'account_bk_code'          => 1201,
                'created_at'               => '2019-12-03 12:00:01',
                'account_group_bk_code'    => 1200,
                'account_group_created_at' => '2019-12-04 12:00:12',
            ],
        ];
        $accounts_expected = $accounts;
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
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

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $accounts_actual = $BookKeeping->retrieveAccounts($bookId);

        $this->assertSame($accounts_expected, $accounts_actual);
    }
}
