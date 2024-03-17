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

class LoadAccountItemsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_loads_the_account_items(): void
    {
        $bookId = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountItemId_1 = (string) Str::uuid();
        $accountItemUpdatedAt_1 = '2024-01-28T19:59:30+09:00';
        $accountGroup_1 = [
            'account_group_id' => $accountGroupId_1,
        ];
        $accountItem_1 = [
            'account_id' => $accountItemId_1,
            'updated_at' => $accountItemUpdatedAt_1,
        ];
        $accountItems = [
            $accountItemId_1 => ['account_id' => $accountItemId_1, 'account' => $accountItem_1],
        ];
        $result_expected = [
            [
                $accountItemId_1 => ['account_id' => $accountItemId_1, 'result' => 'created'],
            ],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateAccountItem')  // call from loadAccountItem
            ->once()
            ->with($accountItem_1)
            ->andReturn($accountItem_1);
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountGroupMock->shouldReceive('searchBookForExporting')  // call from exportAccountItems
            ->once()
            ->with($bookId, $accountGroupId_1)
            ->andReturn([$accountGroup_1]);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);
        $accountMock->shouldReceive('searchAccountGropupForExporting')  // call from exportAccountItems
            ->once()
            ->with($accountGroupId_1)
            ->andReturn([]);
        $accountMock->shouldReceive('createForImporting')  // call from loadAccountItem
            ->once()
            ->with($accountItem_1);

        $service = new AccountMigrationLoaderService($accountMock, $accountGroupMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadAccountItems($bookId, $accountGroupId_1, $accountItems);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_account_group_to_which_the_items_should_be_bound_does_not_exist(): void
    {
        $bookId = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountItemId_1 = (string) Str::uuid();
        $accountItemUpdatedAt_1 = '2024-01-28T19:59:30+09:00';
        $accountItem_1 = [
            'account_id' => $accountItemId_1,
            'updated_at' => $accountItemUpdatedAt_1,
        ];
        $accountItems = [
            $accountItemId_1 => ['account_id' => $accountItemId_1, 'account' => $accountItem_1],
        ];
        $result_expected = [[], 'The account group that the items are bound to does not exist. '.$accountGroupId_1];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountGroupMock->shouldReceive('searchBookForExporting')  // call from exportAccountItems
            ->once()
            ->with($bookId, $accountGroupId_1)
            ->andReturn([]);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);

        $service = new AccountMigrationLoaderService($accountMock, $accountGroupMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadAccountItems($bookId, $accountGroupId_1, $accountItems);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_one_of_the_account_items_does_not_have_its_id(): void
    {
        $bookId = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountItemId_1 = (string) Str::uuid();
        $accountItemUpdatedAt_1 = '2024-01-28T19:59:30+09:00';
        $accountGroup_1 = [
            'account_group_id' => $accountGroupId_1,
        ];
        $accountItem_1 = [
            'account_id' => $accountItemId_1,
            'updated_at' => $accountItemUpdatedAt_1,
        ];
        $accountItems = [
            $accountItemId_1 => ['account' => $accountItem_1],
        ];
        $result_expected = [[], 'invalid data format: account_id'];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountGroupMock->shouldReceive('searchBookForExporting')  // call from exportAccountItems
            ->once()
            ->with($bookId, $accountGroupId_1)
            ->andReturn([$accountGroup_1]);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);
        $accountMock->shouldReceive('searchAccountGropupForExporting')  // call from exportAccountItems
            ->once()
            ->with($accountGroupId_1)
            ->andReturn([]);

        $service = new AccountMigrationLoaderService($accountMock, $accountGroupMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadAccountItems($bookId, $accountGroupId_1, $accountItems);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_one_of_the_account_items_is_in_a_invalid_format(): void
    {
        $bookId = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountItemId_1 = (string) Str::uuid();
        $accountItemUpdatedAt_1 = '2024-01-28T19:59:30+09:00';
        $accountGroup_1 = [
            'account_group_id' => $accountGroupId_1,
        ];
        $accountItem_1 = [
            'updated_at' => $accountItemUpdatedAt_1,
        ];
        $accountItems = [
            $accountItemId_1 => ['account_id' => $accountItemId_1, 'account' => $accountItem_1],
        ];
        $result_expected = [
            [
                $accountItemId_1 => ['account_id' => null, 'result' => null],
            ],
            'invalid data format: account item',
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateAccountItem')  // call from loadAccountItem
            ->once()
            ->with($accountItem_1)
            ->andReturn(null);
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountGroupMock->shouldReceive('searchBookForExporting')  // call from exportAccountItems
            ->once()
            ->with($bookId, $accountGroupId_1)
            ->andReturn([$accountGroup_1]);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);
        $accountMock->shouldReceive('searchAccountGropupForExporting')  // call from exportAccountItems
            ->once()
            ->with($accountGroupId_1)
            ->andReturn([]);

        $service = new AccountMigrationLoaderService($accountMock, $accountGroupMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadAccountItems($bookId, $accountGroupId_1, $accountItems);

        $this->assertSame($result_expected, $result_actual);
    }
}
