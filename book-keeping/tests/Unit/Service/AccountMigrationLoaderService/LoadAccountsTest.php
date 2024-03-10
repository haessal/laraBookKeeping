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

class LoadAccountsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_loads_the_accounts(): void
    {
        $bookId = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountItemId_1 = (string) Str::uuid();
        $accountGroupUpdatedAt_1 = '2024-01-27T19:59:30+09:00';
        $accountItemUpdatedAt_1 = '2024-01-28T19:59:30+09:00';
        $accountGroup_1 = [
            'account_group_id' => $accountGroupId_1,
            'updated_at' => $accountGroupUpdatedAt_1,
        ];
        $accountItem_1 = [
            'account_id' => $accountItemId_1,
            'updated_at' => $accountItemUpdatedAt_1,
        ];
        $account = [
            $accountGroupId_1 => [
                'account_group_id' => $accountGroupId_1,
                'account_group' => $accountGroup_1,
                'items' => [
                    $accountItemId_1 => [
                        'account_id' => $accountItemId_1,
                        'account' => $accountItem_1,
                    ],
                ],
            ],
        ];
        $result_expected = [
            [
                $accountGroupId_1 => [
                    'account_group_id' => $accountGroupId_1,
                    'result' => 'created',
                    'items' => [
                        $accountItemId_1 => [
                            'account_id' => $accountItemId_1,
                            'result' => 'already up-to-date',
                        ],
                    ],
                ],
            ],
            null,
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldReceive('isSourceLater')  // call from loadAccountItem from loadAccountItems
            ->once()
            ->with($accountItemUpdatedAt_1, $accountItemUpdatedAt_1)
            ->andReturn(false);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateAccountGroup')  // call from loadAccountGroup
            ->once()
            ->with($accountGroup_1)
            ->andReturn($accountGroup_1);
        $validatorMock->shouldReceive('validateAccountItem')  // call from loadAccountItem from loadAccountItems
            ->once()
            ->with($accountItem_1)
            ->andReturn($accountItem_1);
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountGroupMock->shouldReceive('searchBookForExporting')  // call from exportAccounts
            ->once()
            ->with($bookId)
            ->andReturn([]);
        $accountGroupMock->shouldReceive('searchBookForExporting')  // call from exportAccountItems from loadAccountItems
            ->once()
            ->with($bookId, $accountGroupId_1)
            ->andReturn($account);
        $accountGroupMock->shouldReceive('createForImporting')  // call from loadAccountGroup
            ->once()
            ->with($accountGroup_1);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);
        $accountMock->shouldReceive('searchAccountGropupForExporting')  // call from exportAccountItems from loadAccountItems
            ->once()
            ->with($accountGroupId_1)
            ->andReturn([$accountItem_1]);

        $service = new AccountMigrationLoaderService($accountMock, $accountGroupMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadAccounts($bookId, $account);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_one_of_the_account_groups_does_not_have_its_id(): void
    {
        $bookId = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $account = [
            $accountGroupId_1 => [],
        ];
        $result_expected = [[], 'invalid data format: account_group_id'];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountGroupMock->shouldReceive('searchBookForExporting')  // call from exportAccounts
            ->once()
            ->with($bookId)
            ->andReturn([]);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);

        $service = new AccountMigrationLoaderService($accountMock, $accountGroupMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadAccounts($bookId, $account);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_one_of_the_account_groups_is_in_a_invalid_format(): void
    {
        $bookId = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountItemId_1 = (string) Str::uuid();
        $accountGroupUpdatedAt_1 = '2024-02-09T10:25:36+09:00';
        $accountItemUpdatedAt_1 = '2024-02-10T10:26:37+09:00';
        $accountGroup_1 = [
            'updated_at' => $accountGroupUpdatedAt_1,
        ];
        $accountItem_1 = [
            'account_id' => $accountItemId_1,
            'updated_at' => $accountItemUpdatedAt_1,
        ];
        $account = [
            $accountGroupId_1 => [
                'account_group_id' => $accountGroupId_1,
                'account_group' => $accountGroup_1,
                'items' => [
                    $accountItemId_1 => [
                        'account_id' => $accountItemId_1,
                        'account' => $accountItem_1,
                    ],
                ],
            ],
        ];
        $result_expected = [
            [
                $accountGroupId_1 => [
                    'account_group_id' => null,
                    'result' => null,
                ],
            ],
            'invalid data format: account group',
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateAccountGroup')  // call from loadAccountGroup
            ->once()
            ->with($accountGroup_1)
            ->andReturn(null);
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountGroupMock->shouldReceive('searchBookForExporting')  // call from exportAccounts
            ->once()
            ->with($bookId)
            ->andReturn([]);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);

        $service = new AccountMigrationLoaderService($accountMock, $accountGroupMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadAccounts($bookId, $account);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_can_not_load_the_account_items_because_the_account_items_is_in_a_invalid_format(): void
    {
        $bookId = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountItemId_1 = (string) Str::uuid();
        $accountGroupUpdatedAt_1 = '2024-02-09T11:25:36+09:00';
        $accountGroup_1 = [
            'account_group_id' => $accountGroupId_1,
            'updated_at' => $accountGroupUpdatedAt_1,
        ];
        $account = [
            $accountGroupId_1 => [
                'account_group_id' => $accountGroupId_1,
                'account_group' => $accountGroup_1,
                'items' => $accountItemId_1,
            ],
        ];
        $result_expected = [
            [
                $accountGroupId_1 => [
                    'account_group_id' => $accountGroupId_1,
                    'result' => 'created',
                ],
            ],
            'invalid data format: account items',
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        /** @var \App\Service\BookKeepingMigrationValidator|\Mockery\MockInterface $validatorMock */
        $validatorMock = Mockery::mock(BookKeepingMigrationValidator::class);
        $validatorMock->shouldReceive('validateAccountGroup')  // call from loadAccountGroup
            ->once()
            ->with($accountGroup_1)
            ->andReturn($accountGroup_1);
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountGroupMock->shouldReceive('searchBookForExporting')  // call from exportAccounts
            ->once()
            ->with($bookId)
            ->andReturn([]);
        $accountGroupMock->shouldReceive('createForImporting')  // call from loadAccountGroup
            ->once()
            ->with($accountGroup_1);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);

        $service = new AccountMigrationLoaderService($accountMock, $accountGroupMock, $toolsMock, $validatorMock);
        $result_actual = $service->loadAccounts($bookId, $account);

        $this->assertSame($result_expected, $result_actual);
    }
}
