<?php

namespace Tests\Unit\Service\BookKeepingService;

use App\Service\AccountService;
use App\Service\BookKeepingService;
use App\Service\BookService;
use App\Service\BudgetService;
use App\Service\SlipService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class UpdateAccountTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_updates_the_account(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 25;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $accountId = (string) Str::uuid();
        $accountGroupId = (string) Str::uuid();
        $newData = ['group' => $accountGroupId, 'title' => 'title1729', 'description' => 'description1729', 'selectable' => false];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, $bookId]);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn([$accountId => []]);
        $accountMock->shouldReceive('retrieveAccountGroups')
            ->once()
            ->with($bookId)
            ->andReturn([$accountGroupId => []]);
        $accountMock->shouldReceive('updateAccount')
            ->once()
            ->with($accountId, $newData);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $BookKeeping->updateAccount($accountId, $newData, $bookId);

        $this->assertTrue(true);
    }
}
