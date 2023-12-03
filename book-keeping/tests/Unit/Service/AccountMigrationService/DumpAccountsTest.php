<?php

namespace Tests\Unit\Service\AccountMigrationService;

use App\DataProvider\AccountGroupRepositoryInterface;
use App\DataProvider\AccountRepositoryInterface;
use App\Service\AccountMigrationService;
use App\Service\BookKeepingMigrationTools;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class DumpAccountsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_export_accounts_as_dump(): void
    {
        $bookId = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountType_1 = 'asset';
        $accountGroupTitle_1 = 'group_title25';
        $accountGroupUid_1 = 1;
        $accountGroupCode_1 = 1100;
        $isCurrent_1 = false;
        $accountGroupDisplayOrder_1 = 2;
        $accountGroupUpdatedAt_1 = '2023-12-03 22:02:02';
        $accountGroupDeleted_1 = false;
        $account_id_1 = (string) Str::uuid();
        $accountTitle_1 = 'title31';
        $description_1 = 'description32';
        $selectable_1 = true;
        $accountUid_1 = 3;
        $accountCode_1 = 1101;
        $accountDisplayOrder_1 = 4;
        $accountUpdatedAt_1 = '2023-12-03 22:02:05';
        $accountDeleted_1 = true;
        $accountGroup_1 = [
            'account_group_id' => $accountGroupId_1,
            'book_id' => $bookId,
            'account_type' => $accountType_1,
            'account_group_title' => $accountGroupTitle_1,
            'bk_uid' => $accountGroupUid_1,
            'account_group_bk_code' => $accountGroupCode_1,
            'is_current' => $isCurrent_1,
            'display_order' => $accountGroupDisplayOrder_1,
            'created_at' => '2023-12-03 22:02:01',
            'updated_at' => $accountGroupUpdatedAt_1,
            'deleted_at' => null,
        ];
        $convertedAccountGroup_1 = [
            'account_group_id' => $accountGroupId_1,
            'book_id' => $bookId,
            'account_type' => $accountType_1,
            'account_group_title' => $accountGroupTitle_1,
            'bk_uid' => $accountGroupUid_1,
            'account_group_bk_code' => $accountGroupCode_1,
            'is_current' => $isCurrent_1,
            'display_order' => $accountGroupDisplayOrder_1,
            'updated_at' => $accountGroupUpdatedAt_1,
            'deleted' => $accountGroupDeleted_1,
        ];
        $accountItem_1 = [
            'account_id' => $account_id_1,
            'account_group_id' => $accountGroupId_1,
            'account_title' => $accountTitle_1,
            'description' => $description_1,
            'selectable' => $selectable_1,
            'bk_uid' => $accountUid_1,
            'account_bk_code' => $accountCode_1,
            'display_order' => $accountDisplayOrder_1,
            'created_at' => '2023-12-03 22:02:04',
            'updated_at' => $accountUpdatedAt_1,
            'deleted_at' => '2023-12-03 22:02:06',
        ];
        $convertedAccountItem_1 = [
            'account_id' => $account_id_1,
            'account_group_id' => $accountGroupId_1,
            'account_title' => $accountTitle_1,
            'description' => $description_1,
            'selectable' => $selectable_1,
            'bk_uid' => $accountUid_1,
            'account_bk_code' => $accountCode_1,
            'display_order' => $accountDisplayOrder_1,
            'updated_at' => $accountUpdatedAt_1,
            'deleted' => $accountDeleted_1,
        ];
        $accounts_expected = [
            [
                'account_group_id' => $accountGroupId_1,
                'account_group' => $convertedAccountGroup_1,
                'items' => [
                    [
                        'account_id' => $account_id_1,
                        'account' => $convertedAccountItem_1,
                    ],
                ],
            ],
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldReceive('convertExportedTimestamps')
            ->once()
            ->with($accountGroup_1)
            ->andReturn($convertedAccountGroup_1);
        $toolsMock->shouldReceive('convertExportedTimestamps')
            ->once()
            ->with($accountItem_1)
            ->andReturn($convertedAccountItem_1);
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountGroupMock->shouldReceive('searchBookForExporting')
            ->once()
            ->with($bookId)
            ->andReturn([$accountGroup_1]);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);
        $accountMock->shouldReceive('searchAccountGropupForExporting')
            ->once()
            ->with($accountGroupId_1)
            ->andReturn([$accountItem_1]);

        $service = new AccountMigrationService($accountMock, $accountGroupMock, $toolsMock);
        $accounts_actual = $service->dumpAccounts($bookId);

        $this->assertSame($accounts_expected, $accounts_actual);
    }
}
