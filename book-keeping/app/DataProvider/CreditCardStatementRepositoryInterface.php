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
     * Create a new credit card statement to import.
     *
     * @param  array{
     *   credit_card_statement_id: string,
     *   book_id: string,
     *   credit_card_statement_outline: string,
     *   credit_card_statement_memo: string|null,
     *   date: string,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }  $newCreditCardStatement
     * @return void
     */
    public function createForImporting(array $newCreditCardStatement);

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
     * Search the book for credit card statements to export.
     *
     * @param  string  $bookId
     * @param  string|null  $creditCardStatementId
     * @return array<int, array<string, mixed>>
     */
    public function searchBookForExporting($bookId, $creditCardStatementId = null): array;

    /**
     * Update the credit card statement.
     *
     * @param  string  $creditCardStatementId
     * @param  array<string, string>  $newData
     * @return void
     */
    public function update($creditCardStatementId, array $newData);

    /**
     * Update the credit card statement to import.
     *
     * @param  array{
     *   credit_card_statement_id: string,
     *   book_id: string,
     *   credit_card_statement_outline: string,
     *   credit_card_statement_memo: string|null,
     *   date: string,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }  $newCreditCardStatement
     * @return void
     */
    public function updateForImporting(array $newCreditCardStatement);
}
