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

class RetrieveCategorizedAccountsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_retrieves_a_list_of_selectable_accounts_for_the_default_book_categorized_into_groups(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 184;
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
                'account_id' => $accountId_1,
                'account_title' => 'accountTitle_1',
                'description' => 'description_1',
                'selectable' => 1,
                'account_bk_code' => 1201,
                'created_at' => '2019-12-02 12:00:01',
                'account_group_bk_code' => 1200,
                'account_group_created_at' => '2019-12-01 12:00:12',
            ],
            $accountId_2 => [
                'account_type' => AccountService::ACCOUNT_TYPE_LIABILITY,
                'account_group_id' => $accountGroupId_2,
                'account_group_title' => 'accountGroupTitle_2',
                'is_current' => 0,
                'account_id' => $accountId_2,
                'account_title' => 'accountTitle_2',
                'description' => 'description_2',
                'selectable' => 1,
                'account_bk_code' => 2302,
                'created_at' => '2019-12-02 12:00:02',
                'account_group_bk_code' => 2300,
                'account_group_created_at' => '2019-12-01 12:00:23',
            ],
            $accountId_3 => [
                'account_type' => AccountService::ACCOUNT_TYPE_LIABILITY,
                'account_group_id' => $accountGroupId_2,
                'account_group_title' => 'accountGroupTitle_2',
                'is_current' => 0,
                'account_id' => $accountId_3,
                'account_title' => 'accountTitle_3',
                'description' => 'description_3',
                'selectable' => 1,
                'account_bk_code' => 2303,
                'created_at' => '2019-12-02 12:00:03',
                'account_group_bk_code' => 2300,
                'account_group_created_at' => '2019-12-01 12:00:23',
            ],
            $accountId_4 => [
                'account_type' => AccountService::ACCOUNT_TYPE_LIABILITY,
                'account_group_id' => $accountGroupId_3,
                'account_group_title' => 'accountGroupTitle_3',
                'is_current' => 0,
                'account_id' => $accountId_4,
                'account_title' => 'accountTitle_4',
                'description' => 'description_4',
                'selectable' => 1,
                'account_bk_code' => 2404,
                'created_at' => '2019-12-02 12:00:04',
                'account_group_bk_code' => 2400,
                'account_group_created_at' => '2019-12-01 12:00:24',
            ],
            $accountId_5 => [
                'account_type' => AccountService::ACCOUNT_TYPE_EXPENSE,
                'account_group_id' => $accountGroupId_4,
                'account_group_title' => 'accountGroupTitle_4',
                'is_current' => 0,
                'account_id' => $accountId_5,
                'account_title' => 'accountTitle_5',
                'description' => 'description_5',
                'selectable' => 0,
                'account_bk_code' => 4105,
                'created_at' => '2019-12-02 12:00:05',
                'account_group_bk_code' => 4100,
                'account_group_created_at' => '2019-12-01 12:00:41',
            ],
            $accountId_6 => [
                'account_type' => AccountService::ACCOUNT_TYPE_REVENUE,
                'account_group_id' => $accountGroupId_5,
                'account_group_title' => 'accountGroupTitle_5',
                'is_current' => 1,
                'account_id' => $accountId_6,
                'account_title' => 'accountTitle_6',
                'description' => 'description_6',
                'selectable' => 1,
                'account_bk_code' => 5106,
                'created_at' => '2019-12-02 12:00:06',
                'account_group_bk_code' => 5100,
                'account_group_created_at' => '2019-12-01 12:00:51',
            ],
            $accountId_7 => [
                'account_type' => AccountService::ACCOUNT_TYPE_REVENUE,
                'account_group_id' => $accountGroupId_5,
                'account_group_title' => 'accountGroupTitle_5',
                'is_current' => 1,
                'account_id' => $accountId_7,
                'account_title' => 'accountTitle_7',
                'description' => 'description_7',
                'selectable' => 0,
                'account_bk_code' => 5107,
                'created_at' => '2019-12-02 12:00:07',
                'account_group_bk_code' => 5100,
                'account_group_created_at' => '2019-12-01 12:00:51',
            ],
            $accountId_8 => [
                'account_type' => AccountService::ACCOUNT_TYPE_REVENUE,
                'account_group_id' => $accountGroupId_5,
                'account_group_title' => 'accountGroupTitle_5',
                'is_current' => 1,
                'account_id' => $accountId_8,
                'account_title' => 'accountTitle_8',
                'description' => 'description_8',
                'selectable' => 1,
                'account_bk_code' => 5108,
                'created_at' => '2019-12-02 12:00:08',
                'account_group_bk_code' => 5100,
                'account_group_created_at' => '2019-12-01 12:00:51',
            ],
        ];
        $accountGroups = [
            $accountGroupId_1 => [
                'account_group_id' => $accountGroupId_1,
                'account_type' => 'asset',
                'account_group_title' => 'accountGroupTitle_1_NG',
                'is_current' => 1,
                'account_group_bk_code' => 1212,
                'created_at' => '2020-05-05 12:00:12',
            ],
            $accountGroupId_6 => [
                'account_group_id' => $accountGroupId_6,
                'account_type' => 'asset',
                'account_group_title' => 'accountGroupTitle_6',
                'is_current' => 1,
                'account_group_bk_code' => null,
                'created_at' => '2020-06-26 12:00:12',
            ],
        ];
        $accounts_menu = [
            'asset' => [
                'groups' => [
                    $accountGroupId_1 => [
                        'title' => 'accountGroupTitle_1',
                        'isCurrent' => 0,
                        'bk_code' => 1200,
                        'createdAt' => '2019-12-01 12:00:12',
                        'items' => [
                            $accountId_1 => [
                                'title' => 'accountTitle_1',
                                'description' => 'description_1',
                                'selectable' => 1,
                                'bk_code' => 1201,
                                'createdAt' => '2019-12-02 12:00:01',
                            ],
                        ],
                    ],
                    $accountGroupId_6 => [
                        'title' => 'accountGroupTitle_6',
                        'isCurrent' => 1,
                        'bk_code' => null,
                        'createdAt' => '2020-06-26 12:00:12',
                        'items' => [],
                    ],
                ],
            ],
            'liability' => [
                'groups' => [
                    $accountGroupId_2 => [
                        'title' => 'accountGroupTitle_2',
                        'isCurrent' => 0,
                        'bk_code' => 2300,
                        'createdAt' => '2019-12-01 12:00:23',
                        'items' => [
                            $accountId_2 => [
                                'title' => 'accountTitle_2',
                                'description' => 'description_2',
                                'selectable' => 1,
                                'bk_code' => 2302,
                                'createdAt' => '2019-12-02 12:00:02',
                            ],
                            $accountId_3 => [
                                'title' => 'accountTitle_3',
                                'description' => 'description_3',
                                'selectable' => 1,
                                'bk_code' => 2303,
                                'createdAt' => '2019-12-02 12:00:03',
                            ],
                        ],
                    ],
                    $accountGroupId_3 => [
                        'title' => 'accountGroupTitle_3',
                        'isCurrent' => 0,
                        'bk_code' => 2400,
                        'createdAt' => '2019-12-01 12:00:24',
                        'items' => [
                            $accountId_4 => [
                                'title' => 'accountTitle_4',
                                'description' => 'description_4',
                                'selectable' => 1,
                                'bk_code' => 2404,
                                'createdAt' => '2019-12-02 12:00:04',
                            ],
                        ],
                    ],
                ],
            ],
            'expense' => [
                'groups' => [],
            ],
            'revenue' => [
                'groups' => [
                    $accountGroupId_5 => [
                        'title' => 'accountGroupTitle_5',
                        'isCurrent' => 1,
                        'bk_code' => 5100,
                        'createdAt' => '2019-12-01 12:00:51',
                        'items' => [
                            $accountId_6 => [
                                'title' => 'accountTitle_6',
                                'description' => 'description_6',
                                'selectable' => 1,
                                'bk_code' => 5106,
                                'createdAt' => '2019-12-02 12:00:06',
                            ],
                            $accountId_8 => [
                                'title' => 'accountTitle_8',
                                'description' => 'description_8',
                                'selectable' => 1,
                                'bk_code' => 5108,
                                'createdAt' => '2019-12-02 12:00:08',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $result_expected = [BookKeepingService::STATUS_NORMAL, $accounts_menu];
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
        $accountMock->shouldReceive('retrieveAccountGroups')
            ->once()
            ->with($bookId)
            ->andReturn($accountGroups);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->retrieveCategorizedAccounts(true);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_specified_book_is_not_readable(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 304;
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
        $accountMock->shouldNotReceive('retrieveAccountGroups');
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->retrieveCategorizedAccounts(false, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }
}
