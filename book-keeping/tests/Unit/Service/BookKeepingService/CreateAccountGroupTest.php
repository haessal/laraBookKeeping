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

class CreateAccountGroupTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_creates_a_new_account_group(): void
    {
        $bookId = (string) Str::uuid();
        $accountType = 'asset';
        $title = 'title60';
        $accountGroupId_expected = (string) Str::uuid();
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('createAccountGroup')
            ->once()
            ->with($bookId, $accountType, $title)
            ->andReturn($accountGroupId_expected);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $accountGroupId_actual = $BookKeeping->createAccountGroup($accountType, $title, $bookId);

        $this->assertSame($accountGroupId_expected, $accountGroupId_actual);
    }
}
