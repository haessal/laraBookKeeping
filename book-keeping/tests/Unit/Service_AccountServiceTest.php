<?php

namespace Tests\Unit;

use App\DataProvider\AccountGroupRepositoryInterface;
use App\DataProvider\AccountRepositoryInterface;
use App\Service\AccountService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Service_AccountServiceTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function createAccount_CallRepositoryWithArgumentsAsItIs()
    {
        $accountGroupId = (string) Str::uuid();
        $title = 'title';
        $description = 'dscription';
        $bk_uid = 22;
        $bk_code = 1101;
        $accountId_expected = (string) Str::uuid();
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);
        $accountMock->shouldReceive('create')
            ->once()
            ->with($accountGroupId, $title, $description, $bk_uid, $bk_code)
            ->andReturn($accountId_expected);

        $account = new AccountService($accountMock, $accountGroupMock);
        $accountId_actual = $account->createAccount($accountGroupId, $title, $description, $bk_uid, $bk_code);

        $this->assertSame($accountId_expected, $accountId_actual);
    }

    /**
     * @test
     */
    public function createAccountGroup_CallRepositoryWithArgumentsAsItIs()
    {
        $bookId = (string) Str::uuid();
        $accountType = 'asset';
        $title = 'title';
        $isCurrent = true;
        $bk_uid = 22;
        $bk_code = 1101;
        $accountGroupId_expected = (string) Str::uuid();
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);
        $accountGroupMock->shouldReceive('create')
            ->once()
            ->with($bookId, $accountType, $title, $isCurrent, $bk_uid, $bk_code)
            ->andReturn($accountGroupId_expected);

        $account = new AccountService($accountMock, $accountGroupMock);
        $accountGroupId_actual = $account->createAccountGroup($bookId, $accountType, $title, $isCurrent, $bk_uid, $bk_code);

        $this->assertSame($accountGroupId_expected, $accountGroupId_actual);
    }

    /**
     * @test
     */
    public function retrieveAccounts_CallRepositoryAndChangeArrayFormat()
    {
        $bookId = (string) Str::uuid();
        $account_id_1 = (string) Str::uuid();
        $account_id_2 = (string) Str::uuid();
        $account_id_3 = (string) Str::uuid();
        $accountItem_1 = ['account_id' => $account_id_1, 'account_title' => 'title1', 'selectable' => true, 'account_bk_code' => 1101];
        $accountItem_2 = ['account_id' => $account_id_2, 'account_title' => 'title2', 'selectable' => true, 'account_bk_code' => 1201];
        $accountItem_3 = ['account_id' => $account_id_3, 'account_title' => 'title3', 'selectable' => false, 'account_bk_code' => 1102];
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);
        $accountMock->shouldReceive('searchAccount')
            ->once()
            ->with($bookId)
            ->andReturn([$accountItem_1, $accountItem_2, $accountItem_3]);
        $accounts_expected = [$account_id_1 => $accountItem_1, $account_id_2 => $accountItem_2, $account_id_3 => $accountItem_3];

        $account = new AccountService($accountMock, $accountGroupMock);
        $accounts_actual = $account->retrieveAccounts($bookId);

        $this->assertSame($accounts_expected, $accounts_actual);
    }

    /**
     * @test
     */
    public function retrieveAccountGroups_CallRepositoryAndChangeArrayFormat()
    {
        $bookId = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountGroupId_2 = (string) Str::uuid();
        $accountGroupId_3 = (string) Str::uuid();
        $accountGroup_1 = [
            'account_group_id'      => $accountGroupId_1,
            'account_type'          => 'asset',
            'account_group_title'   => 'group_title_1',
            'is_current'            => true,
            'account_group_bk_code' => 1100,
            'created_at'            => '2020-06-01 12:00:20',
        ];
        $accountGroup_2 = [
            'account_group_id'      => $accountGroupId_2,
            'account_type'          => 'asset',
            'account_group_title'   => 'group_title_2',
            'is_current'            => false,
            'account_group_bk_code' => 1200,
            'created_at'            => '2020-06-02 12:00:20',
        ];
        $accountGroup_3 = [
            'account_group_id'      => $accountGroupId_3,
            'account_type'          => 'liability',
            'account_group_title'   => 'group_title_3',
            'is_current'            => false,
            'account_group_bk_code' => 2100,
            'created_at'            => '2020-06-02 12:00:20',
        ];
        $accountGroups_expected = [$accountGroupId_1 => $accountGroup_1, $accountGroupId_2 => $accountGroup_2, $accountGroupId_3 => $accountGroup_3];
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountGroupMock->shouldReceive('search')
            ->once()
            ->with($bookId)
            ->andReturn([$accountGroup_1, $accountGroup_2, $accountGroup_3]);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);

        $account = new AccountService($accountMock, $accountGroupMock);
        $accountGroups_actual = $account->retrieveAccountGroups($bookId);

        $this->assertSame($accountGroups_expected, $accountGroups_actual);
    }

    /**
     * @test
     */
    public function updateAccount_CallRepositoryWithArgumentsAsItIs()
    {
        $accountId = (string) Str::uuid();
        $accountGroupId = (string) Str::uuid();
        $newData = ['group' => $accountGroupId, 'title' => 'title106', 'description' => 'description106', 'selectable' => false];
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);
        $accountMock->shouldReceive('update')
            ->once()
            ->with($accountId, $newData);

        $account = new AccountService($accountMock, $accountGroupMock);
        $account->updateAccount($accountId, $newData);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function updateAccountGroup_CallRepositoryWithArgumentsAsItIs()
    {
        $accountGroupId = (string) Str::uuid();
        $newData = ['title' => 'title127', 'is_current' => true];
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountGroupMock->shouldReceive('update')
            ->once()
            ->with($accountGroupId, $newData);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);

        $account = new AccountService($accountMock, $accountGroupMock);
        $account->updateAccountGroup($accountGroupId, $newData);

        $this->assertTrue(true);
    }
}
