<?php

namespace App\DataProvider;

interface BudgetRepositoryInterface
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
    public function create(string $bookId, string $accountId, string $date, int $amount): string;
}
