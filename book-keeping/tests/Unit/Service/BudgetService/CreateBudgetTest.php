<?php

namespace Tests\Unit\Service\BudgetService;

use App\DataProvider\BudgetRepositoryInterface;
use App\Service\BudgetService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class CreateBudgetTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_create_a_new_budget(): void
    {
        $bookId = (string) Str::uuid();
        $accountId = (string) Str::uuid();
        $date = '2019-09-01';
        $amount = 10000;
        $budgetId_expected = (string) Str::uuid();
        /** @var \App\DataProvider\BudgetRepositoryInterface|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetRepositoryInterface::class);
        $budgetMock->shouldReceive('create')
            ->once()
            ->with($bookId, $accountId, $date, $amount)
            ->andReturn($budgetId_expected);

        $budget = new BudgetService($budgetMock);
        $budgetId_actual = $budget->createBudget($bookId, $accountId, $date, $amount);

        $this->assertSame($budgetId_expected, $budgetId_actual);
    }
}
