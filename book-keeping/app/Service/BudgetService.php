<?php

namespace App\Service;

use App\DataProvider\BudgetRepositoryInterface;

class BudgetService
{
    /**
     * Budget repository instance.
     *
     * @var \App\DataProvider\BudgetRepositoryInterface
     */
    private $budget;

    /**
     * Create a new BudgetService instance.
     *
     * @param  \App\DataProvider\BudgetRepositoryInterface  $budget
     */
    public function __construct(BudgetRepositoryInterface $budget)
    {
        $this->budget = $budget;
    }

    /**
     * Create a new budget.
     *
     * @param  string  $bookId
     * @param  string  $accountId
     * @param  string  $date
     * @param  int  $amount
     * @return string
     */
    public function createBudget($bookId, $accountId, $date, $amount)
    {
        $budgetId = $this->budget->create($bookId, $accountId, $date, $amount);

        return $budgetId;
    }
}
