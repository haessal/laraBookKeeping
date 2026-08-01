<?php

namespace App\Repositories\Eloquent;

use App\Models\DataProvider\Eloquent\Budget;
use App\Repositories\BudgetRepositoryInterface;

class BudgetRepository implements BudgetRepositoryInterface
{
    /**
     * Create a new budget to be bound in the book.
     *
     * @param  string  $bookId
     * @param  string  $accountId
     * @param  string  $date
     * @param  int  $amount
     * @return string
     */
    public function create($bookId, $accountId, $date, $amount)
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
