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

class CreateAccountTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_creates_a_new_account(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 25;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $accountGroupId = (string) Str::uuid();
        $title = 'title31';
        $description = 'description32';
        $accountId = (string) Str::uuid();
        $result_expected = [BookKeepingService::STATUS_NORMAL, $accountId];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, $bookId]);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccountGroups')
            ->once()
            ->with($bookId)
            ->andReturn([$accountGroupId => []]);
        $accountMock->shouldReceive('createAccount')
            ->once()
            ->with($accountGroupId, $title, $description)
            ->andReturn($accountId);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->createAccount($accountGroupId, $title, $description, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }
}
