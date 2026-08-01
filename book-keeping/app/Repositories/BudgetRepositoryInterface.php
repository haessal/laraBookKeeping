<?php

namespace App\Repositories;

interface BudgetRepositoryInterface
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
    public function create($bookId, $accountId, $date, $amount);
}
