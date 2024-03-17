<?php

namespace Tests\Unit\Service\AccountMigrationLoaderService;

use App\DataProvider\AccountGroupRepositoryInterface;
use App\DataProvider\AccountRepositoryInterface;
use App\Service\AccountMigrationLoaderService;
use App\Service\BookKeepingMigrationTools;
use App\Service\BookKeepingMigrationValidator;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class LoadAccountGroupTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_create_the_account_group(): void
    {
        $bookId = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountType_1 = 'asset';
        $accountGroupTitle_1 = 'group_title26';
        $accountGroupUid_1 = 1;
        $accountGroupCode_1 = 1100;
        $isCurrent_1 = false;
        $accountGroupDisplayOrder_1 = 2;
        $accountGroupUpdatedAt_1 = '2024-01-03T23:07:31+09:00';
        $accountGroupDeleted_1 = false;
        $accountGroup_1 = [
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
        $result_expected = [
            ['account_group_id' => $accountGroupId_1, 'result' => 'created'],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldNotReceive('isSourceLater');
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateAccountGroup')
            ->once()
            ->with($accountGroup_1)
            ->andReturn($accountGroup_1);
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountGroupMock->shouldNotReceive('updateForImporting');
        $accountGroupMock->shouldReceive('createForImporting')
            ->once()
            ->with($accountGroup_1);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);

        $service = new AccountMigrationLoaderService($accountMock, $accountGroupMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadAccountGroup($accountGroup_1, []);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_calls_repository_to_update_the_account_group(): void
    {
        $bookId = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountType_1 = 'asset';
        $accountGroupTitle_1 = 'group_title26';
        $accountGroupUid_1 = 1;
        $accountGroupCode_1 = 1100;
        $isCurrent_1 = false;
        $accountGroupDisplayOrder_1 = 2;
        $accountGroupUpdatedAt_1 = '2024-01-03T23:07:31+09:00';
        $accountGroupDeleted_1 = false;
        $destinationUpdateAt_1 = '2024-01-02T23:07:31+09:00';
        $accountGroup_1 = [
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
        $destinationAccountGroups_1 = [
            $accountGroupId_1 => [
                'account_group_id' => $accountGroupId_1,
                'updated_at' => $destinationUpdateAt_1,
            ],
        ];
        $result_expected = [
            ['account_group_id' => $accountGroupId_1, 'result' => 'updated'],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldReceive('isSourceLater')
            ->once()
            ->with($accountGroupUpdatedAt_1, $destinationUpdateAt_1)
            ->andReturn(true);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateAccountGroup')
            ->once()
            ->with($accountGroup_1)
            ->andReturn($accountGroup_1);
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountGroupMock->shouldReceive('updateForImporting')
            ->once()
            ->with($accountGroup_1);
        $accountGroupMock->shouldNotReceive('createForImporting');
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);

        $service = new AccountMigrationLoaderService($accountMock, $accountGroupMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadAccountGroup($accountGroup_1, $destinationAccountGroups_1);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_account_group_is_already_up_to_date(): void
    {
        $bookId = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountType_1 = 'asset';
        $accountGroupTitle_1 = 'group_title26';
        $accountGroupUid_1 = 1;
        $accountGroupCode_1 = 1100;
        $isCurrent_1 = false;
        $accountGroupDisplayOrder_1 = 2;
        $accountGroupUpdatedAt_1 = '2024-01-03T23:07:31+09:00';
        $accountGroupDeleted_1 = false;
        $accountGroup_1 = [
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
        $destinationAccountGroups_1 = [
            $accountGroupId_1 => [
                'account_group_id' => $accountGroupId_1,
                'updated_at' => $accountGroupUpdatedAt_1,
            ],
        ];

        $result_expected = [
            ['account_group_id' => $accountGroupId_1, 'result' => 'already up-to-date'],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldReceive('isSourceLater')
            ->once()
            ->with($accountGroupUpdatedAt_1, $accountGroupUpdatedAt_1)
            ->andReturn(false);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateAccountGroup')
            ->once()
            ->with($accountGroup_1)
            ->andReturn($accountGroup_1);
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountGroupMock->shouldNotReceive('updateForImporting');
        $accountGroupMock->shouldNotReceive('createForImporting');
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);

        $service = new AccountMigrationLoaderService($accountMock, $accountGroupMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadAccountGroup($accountGroup_1, $destinationAccountGroups_1);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_account_group_is_not_valid(): void
    {
        $result_expected = [
            ['account_group_id' => null, 'result' => null],
            'invalid data format: account group',
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldNotReceive('isSourceLater');
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateAccountGroup')
            ->once()
            ->with([])
            ->andReturn(null);
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountGroupMock->shouldNotReceive('updateForImporting');
        $accountGroupMock->shouldNotReceive('createForImporting');
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);

        $service = new AccountMigrationLoaderService($accountMock, $accountGroupMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadAccountGroup([], []);

        $this->assertSame($result_expected, $result_actual);
    }
}
