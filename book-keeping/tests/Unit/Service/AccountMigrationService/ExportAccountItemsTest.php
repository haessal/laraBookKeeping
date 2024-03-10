<?php

namespace Tests\Unit\Service\AccountMigrationService;

use App\DataProvider\AccountGroupRepositoryInterface;
use App\DataProvider\AccountRepositoryInterface;
use App\Service\AccountMigrationService;
use App\Service\BookKeepingMigrationTools;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class ExportAccountItemsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_export_accounts(): void
    {
        $bookId = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $account_id_1 = (string) Str::uuid();
        $accountUpdatedAt_1 = '2023-12-21T23:28:05+09:00';
        $accountGroup_1 = [
            'account_group_id' => $accountGroupId_1,
        ];
        $accountItem_1 = [
            'account_id' => $account_id_1,
            'updated_at' => $accountUpdatedAt_1,
        ];
        $accounts_expected = [
            $accountGroupId_1 => [
                'items' => [
                    $account_id_1 => [
                        'account_id' => $account_id_1,
                        'updated_at' => $accountUpdatedAt_1,
                    ],
                ],
            ],
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountGroupMock->shouldReceive('searchBookForExporting')
            ->once()
            ->with($bookId, $accountGroupId_1)
            ->andReturn([$accountGroup_1]);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);
        $accountMock->shouldReceive('searchAccountGropupForExporting')
            ->once()
            ->with($accountGroupId_1)
            ->andReturn([$accountItem_1]);

        $service = new AccountMigrationService($accountMock, $accountGroupMock, $toolsMock);
        $accounts_actual = $service->exportAccountItems($bookId, $accountGroupId_1);

        $this->assertSame($accounts_expected, $accounts_actual);
    }
}
