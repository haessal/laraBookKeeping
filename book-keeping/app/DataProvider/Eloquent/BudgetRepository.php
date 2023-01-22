<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\BudgetRepositoryInterface;
use App\Models\Budget;

class BudgetRepository implements BudgetRepositoryInterface
{
    /**
     * Create a budget to be bound in the book.
     *
     * @param  string  $bookId
     * @param  string  $accountId
     * @param  string  $date
     * @param  int  $amount
     * @return string
     */
    public function create(string $bookId, string $accountId, string $date, int $amount): string
    {
        $budget = new Budget();
        $budget->book_id = $bookId;
        $budget->account_code = $accountId;
        $budget->date = $date;
        $budget->amount = $amount;
        $budget->save();

        return $budget->budget_id;
    }
}
