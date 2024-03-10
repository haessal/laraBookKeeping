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

class LoadAccountItemTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_create_the_account_item(): void
    {
        $accountGroupId_1 = (string) Str::uuid();
        $accountItemId_1 = (string) Str::uuid();
        $accountItemTitle_1 = 'item_title25';
        $description_1 = 'description_26';
        $selectable_1 = true;
        $accountItemUid_1 = 6;
        $accountItemCode_1 = 1101;
        $accountItemDisplayOrder_1 = 2;
        $accountItemUpdatedAt_1 = '2024-01-28T18:54:30+09:00';
        $accountItemDeleted_1 = false;
        $accountItem_1 = [
            'account_id' => $accountItemId_1,
            'account_group_id' => $accountGroupId_1,
            'account_title' => $accountItemTitle_1,
            'description' => $description_1,
            'selectable' => $selectable_1,
            'bk_uid' => $accountItemUid_1,
            'account_bk_code' => $accountItemCode_1,
            'display_order' => $accountItemDisplayOrder_1,
            'updated_at' => $accountItemUpdatedAt_1,
            'deleted' => $accountItemDeleted_1,
        ];
        $result_expected = [
            ['account_id' => $accountItemId_1, 'result' => 'created'],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldNotReceive('isSourceLater');
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateAccountItem')
            ->once()
            ->with($accountItem_1)
            ->andReturn($accountItem_1);
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);
        $accountMock->shouldNotReceive('updateForImporting');
        $accountMock->shouldReceive('createForImporting')
            ->once()
            ->with($accountItem_1);

        $service = new AccountMigrationLoaderService($accountMock, $accountGroupMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadAccountItem($accountItem_1, []);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_calls_repository_to_update_the_account_item(): void
    {
        $accountGroupId_1 = (string) Str::uuid();
        $accountItemId_1 = (string) Str::uuid();
        $accountItemTitle_1 = 'item_title76';
        $description_1 = 'description_77';
        $selectable_1 = true;
        $accountItemUid_1 = 9;
        $accountItemCode_1 = 1102;
        $accountItemDisplayOrder_1 = 1;
        $accountItemUpdatedAt_1 = '2024-01-28T18:54:30+09:00';
        $accountItemDeleted_1 = false;
        $destinationUpdateAt_1 = '2024-01-27T18:54:30+09:00';
        $accountItem_1 = [
            'account_id' => $accountItemId_1,
            'account_group_id' => $accountGroupId_1,
            'account_title' => $accountItemTitle_1,
            'description' => $description_1,
            'selectable' => $selectable_1,
            'bk_uid' => $accountItemUid_1,
            'account_bk_code' => $accountItemCode_1,
            'display_order' => $accountItemDisplayOrder_1,
            'updated_at' => $accountItemUpdatedAt_1,
            'deleted' => $accountItemDeleted_1,
        ];
        $destinationAccountItems_1 = [
            $accountItemId_1 => [
                'account_id' => $accountItemId_1,
                'updated_at' => $destinationUpdateAt_1,
            ],
        ];
        $result_expected = [
            ['account_id' => $accountItemId_1, 'result' => 'updated'],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldReceive('isSourceLater')
            ->once()
            ->with($accountItemUpdatedAt_1, $destinationUpdateAt_1)
            ->andReturn(true);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateAccountItem')
            ->once()
            ->with($accountItem_1)
            ->andReturn($accountItem_1);
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);
        $accountMock->shouldReceive('updateForImporting')
            ->once()
            ->with($accountItem_1);
        $accountMock->shouldNotReceive('createForImporting');

        $service = new AccountMigrationLoaderService($accountMock, $accountGroupMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadAccountItem($accountItem_1, $destinationAccountItems_1);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_account_item_is_already_up_to_date(): void
    {
        $accountGroupId_1 = (string) Str::uuid();
        $accountItemId_1 = (string) Str::uuid();
        $accountItemTitle_1 = 'item_title137';
        $description_1 = 'description_138';
        $selectable_1 = true;
        $accountItemUid_1 = 9;
        $accountItemCode_1 = 1102;
        $accountItemDisplayOrder_1 = 1;
        $accountItemUpdatedAt_1 = '2024-01-27T19:54:30+09:00';
        $accountItemDeleted_1 = false;
        $destinationUpdateAt_1 = '2024-01-26T18:54:30+09:00';
        $accountItem_1 = [
            'account_id' => $accountItemId_1,
            'account_group_id' => $accountGroupId_1,
            'account_title' => $accountItemTitle_1,
            'description' => $description_1,
            'selectable' => $selectable_1,
            'bk_uid' => $accountItemUid_1,
            'account_bk_code' => $accountItemCode_1,
            'display_order' => $accountItemDisplayOrder_1,
            'updated_at' => $accountItemUpdatedAt_1,
            'deleted' => $accountItemDeleted_1,
        ];
        $destinationAccountItems_1 = [
            $accountItemId_1 => [
                'account_id' => $accountItemId_1,
                'updated_at' => $destinationUpdateAt_1,
            ],
        ];
        $result_expected = [
            ['account_id' => $accountItemId_1, 'result' => 'already up-to-date'],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldReceive('isSourceLater')
            ->once()
            ->with($accountItemUpdatedAt_1, $destinationUpdateAt_1)
            ->andReturn(false);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateAccountItem')
            ->once()
            ->with($accountItem_1)
            ->andReturn($accountItem_1);
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);
        $accountMock->shouldNotReceive('updateForImporting');
        $accountMock->shouldNotReceive('createForImporting');

        $service = new AccountMigrationLoaderService($accountMock, $accountGroupMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadAccountItem($accountItem_1, $destinationAccountItems_1);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_account_item_is_not_valid(): void
    {
        $result_expected = [
            ['account_id' => null, 'result' => null],
            'invalid data format: account item',
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldNotReceive('isSourceLater');
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateAccountItem')
            ->once()
            ->with([])
            ->andReturn(null);
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);
        $accountMock->shouldNotReceive('updateForImporting');
        $accountMock->shouldNotReceive('createForImporting');

        $service = new AccountMigrationLoaderService($accountMock, $accountGroupMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadAccountItem([], []);

        $this->assertSame($result_expected, $result_actual);
    }
}
