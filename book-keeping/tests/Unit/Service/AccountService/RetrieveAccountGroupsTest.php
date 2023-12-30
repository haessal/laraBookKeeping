<?php

namespace Tests\Unit\Service\AccountService;

use App\DataProvider\AccountGroupRepositoryInterface;
use App\DataProvider\AccountRepositoryInterface;
use App\Service\AccountService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveAccountGroupsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_retrieve_a_list_of_account_groups(): void
    {
        $bookId = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountGroupId_2 = (string) Str::uuid();
        $accountGroupId_3 = (string) Str::uuid();
        $accountGroup_1 = [
            'account_group_id' => $accountGroupId_1,
            'account_type' => 'asset',
            'account_group_title' => 'group_title_1',
            'is_current' => true,
            'account_group_bk_code' => 1100,
            'created_at' => '2020-06-01 12:00:20',
        ];
        $accountGroup_2 = [
            'account_group_id' => $accountGroupId_2,
            'account_type' => 'asset',
            'account_group_title' => 'group_title_2',
            'is_current' => false,
            'account_group_bk_code' => 1200,
            'created_at' => '2020-06-02 12:00:20',
        ];
        $accountGroup_3 = [
            'account_group_id' => $accountGroupId_3,
            'account_type' => 'liability',
            'account_group_title' => 'group_title_3',
            'is_current' => false,
            'account_group_bk_code' => 2100,
            'created_at' => '2020-06-02 12:00:20',
        ];
        $accountGroups_expected = [$accountGroupId_1 => $accountGroup_1, $accountGroupId_2 => $accountGroup_2, $accountGroupId_3 => $accountGroup_3];
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountGroupMock->shouldReceive('searchBook')
            ->once()
            ->with($bookId)
            ->andReturn([$accountGroup_1, $accountGroup_2, $accountGroup_3]);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);

        $account = new AccountService($accountMock, $accountGroupMock);
        $accountGroups_actual = $account->retrieveAccountGroups($bookId);

        $this->assertSame($accountGroups_expected, $accountGroups_actual);
    }
}
