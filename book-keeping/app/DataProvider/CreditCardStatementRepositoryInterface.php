<?php

namespace App\DataProvider;

interface CreditCardStatementRepositoryInterface
{
    /**
     * Create a new credit card statement to be bound in the book.
     *
     * @param  string  $bookId
     * @param  string  $outline
     * @param  string|null  $memo
     * @param  string  $date
     * @param  int|null  $displayOrder
     * @return string
     */
    public function create($bookId, $outline, $memo, $date, $displayOrder);

    /**
     * Delete the credit card statement.
     *
     * @param  string  $creditCardStatementId
     * @return void
     */
    public function delete($creditCardStatementId);

    /**
     * Search the book for credit card statement.
     *
     * @param  string  $bookId
     * @param  string|null  $creditCardStatementId
     * @return array<int, array<string, mixed>>
     */
    public function searchBook($bookId, $creditCardStatementId): array;

    /**
     * Update the credit card statement.
     *
     * @param  string  $creditCardStatementId
     * @param  array<string, string>  $newData
     * @return void
     */
    public function update($creditCardStatementId, array $newData);
}
