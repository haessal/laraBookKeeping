<?php

namespace Tests\Unit\Service\AccountService;

use App\DataProvider\AccountGroupRepositoryInterface;
use App\DataProvider\AccountRepositoryInterface;
use App\Service\AccountService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class UpdateAccountGroupTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_update_the_account_group(): void
    {
        $accountGroupId = (string) Str::uuid();
        $newData = ['title' => 'title127', 'is_current' => true];
        /** @var \App\DataProvider\AccountGroupRepositoryInterface|\Mockery\MockInterface $accountGroupMock */
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountGroupMock->shouldReceive('update')
            ->once()
            ->with($accountGroupId, $newData);
        /** @var \App\DataProvider\AccountRepositoryInterface|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);

        $account = new AccountService($accountMock, $accountGroupMock);
        $account->updateAccountGroup($accountGroupId, $newData);

        $this->assertTrue(true);
    }
}
