<?php

namespace Tests\Unit\Service\AccountService;

use App\DataProvider\AccountGroupRepositoryInterface;
use App\DataProvider\AccountRepositoryInterface;
use App\Service\AccountService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class CreateAccountGroupTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_create_a_new_account_group(): void
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
}
