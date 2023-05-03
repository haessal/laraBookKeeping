<?php

namespace Tests\Unit\Service\AccountService;

use App\DataProvider\AccountGroupRepositoryInterface;
use App\DataProvider\AccountRepositoryInterface;
use App\Service\AccountService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class CreateAccountTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_create_a_new_account(): void
    {
        $accountGroupId = (string) Str::uuid();
        $title = 'title';
        $description = 'description';
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
}
