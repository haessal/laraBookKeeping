<?php

namespace Tests\Unit\Service\AccountService;

use App\DataProvider\AccountGroupRepositoryInterface;
use App\DataProvider\AccountRepositoryInterface;
use App\Service\AccountService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveAccountsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_retrieve_a_list_of_accounts(): void
    {
        $bookId = (string) Str::uuid();
        $account_id_1 = (string) Str::uuid();
        $account_id_2 = (string) Str::uuid();
        $account_id_3 = (string) Str::uuid();
        $accountItem_1 = [
            'account_type'             => 'asset',
            'account_group_id'         => (string) Str::uuid(),
            'account_group_title'      => 'group_title1',
            'is_current'               => true,
            'account_id'               => $account_id_1,
            'account_title'            => 'title1',
            'description'              => 'description1',
            'selectable'               => true,
            'account_bk_code'          => 1101,
            'created_at'               => '2023-05-01 09:59:01',
            'account_group_bk_code'    => 1100,
            'account_group_created_at' => '2023-05-01 09:58:01',
        ];
        $accountItem_2 = [
            'account_type'             => 'liability',
            'account_group_id'         => (string) Str::uuid(),
            'account_group_title'      => 'group_title2',
            'is_current'               => true,
            'account_id'               => $account_id_2,
            'account_title'            => 'title2',
            'description'              => 'description2',
            'selectable'               => true,
            'account_bk_code'          => 1201,
            'created_at'               => '2023-05-01 09:49:01',
            'account_group_bk_code'    => 1200,
            'account_group_created_at' => '2023-05-01 09:48:01',
        ];
        $accountItem_3 = [
            'account_type'             => 'expense',
            'account_group_id'         => (string) Str::uuid(),
            'account_group_title'      => 'group_title3',
            'is_current'               => true,
            'account_id'               => $account_id_3,
            'account_title'            => 'title3',
            'description'              => 'description3',
            'selectable'               => false,
            'account_bk_code'          => 1102,
            'created_at'               => '2023-05-01 09:39:01',
            'account_group_bk_code'    => 1100,
            'account_group_created_at' => '2023-05-01 09:38:01',
        ];
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);
        $accountMock->shouldReceive('searchBook')
            ->once()
            ->with($bookId)
            ->andReturn([$accountItem_1, $accountItem_2, $accountItem_3]);
        $accounts_expected = [$account_id_1 => $accountItem_1, $account_id_2 => $accountItem_2, $account_id_3 => $accountItem_3];

        $account = new AccountService($accountMock, $accountGroupMock);
        $accounts_actual = $account->retrieveAccounts($bookId);

        $this->assertSame($accounts_expected, $accounts_actual);
    }
}
