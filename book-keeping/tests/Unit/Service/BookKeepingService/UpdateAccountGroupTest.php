<?php

namespace Tests\Unit\Service\BookKeepingService;

use App\Service\AccountService;
use App\Service\BookKeepingService;
use App\Service\BookService;
use App\Service\BudgetService;
use App\Service\SlipService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class UpdateAccountGroupTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_updates_the_account_group(): void
    {
        $bookId = (string) Str::uuid();
        $accountGroupId = (string) Str::uuid();
        $newData = ['title' => 'title1755', 'is_current' => true];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('updateAccountGroup')
            ->once()
            ->with($accountGroupId, $newData);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $BookKeeping->updateAccountGroup($accountGroupId, $newData, $bookId);

        $this->assertTrue(true);
    }
}
