<?php

namespace Tests\Unit\Service\AccountMigrationService;

use App\Repositories\AccountGroupRepositoryInterface;
use App\Repositories\AccountRepositoryInterface;
use App\Service\AccountMigrationService;
use App\Service\BookKeepingMigrationTools;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class ExportAccountsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_calls_repository_to_export_accounts(): void
    {
        $bookId = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountGroupUpdatedAt_1 = '2023-12-22T23:28:05+09:00';
        $convertedAccountGroupUpdatedAt_1 = Carbon::parse($accountGroupUpdatedAt_1)->timezone('UTC')->toAtomString();
        $accountGroup_1 = [
            'account_group_id' => $accountGroupId_1,
            'updated_at' => $accountGroupUpdatedAt_1,
        ];
        $convertedAccountGroup_1 = [
            'account_group_id' => $accountGroupId_1,
            'updated_at' => $convertedAccountGroupUpdatedAt_1,
        ];
        $accounts_expected = [
            $accountGroupId_1 => [
                'account_group_id' => $accountGroupId_1,
                'updated_at' => $convertedAccountGroupUpdatedAt_1,
            ],
        ];
        /** @var \App\Service\BookKeepingMigrationTools|\Mockery\MockInterface $toolsMock */
        $toolsMock = Mockery::mock(BookKeepingMigrationTools::class);
        $toolsMock->shouldReceive('convertExportedTimestamp')
            ->once()
            ->with($accountGroupUpdatedAt_1)
            ->andReturn($convertedAccountGroupUpdatedAt_1);
        //$toolsMock->shouldReceive('convertExportedTimestamps')
        //    ->once()
        //    ->with([$accountGroup_1])
        //    ->andReturn([$convertedAccountGroup_1]);
        /** @var \App\Repositories\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountGroupMock->shouldReceive('searchBookForExporting')
            ->once()
            ->with($bookId)
            ->andReturn([$accountGroup_1]);
        /** @var \App\Repositories\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);

        $service = new AccountMigrationService($accountMock, $accountGroupMock, $toolsMock);
        $accounts_actual = $service->exportAccounts($bookId);

        $this->assertSame($accounts_expected, $accounts_actual);
    }
}
