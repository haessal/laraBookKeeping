<?php

namespace App\Service;

use App\Repositories\CreditCardStatementRepositoryInterface;

class CreditCardStatementService
{
    /**
     * Credit card statement repository instance.
     *
     * @var \App\Repositories\CreditCardStatementRepositoryInterface
     */
    protected $creditCardStatement;

    /**
     * Create a new CreditCardStatementService instance.
     *
     * @param  \App\Repositories\CreditCardStatementRepositoryInterface  $creditCardStatement
     */
    public function __construct(CreditCardStatementRepositoryInterface $creditCardStatement)
    {
        $this->creditCardStatement = $creditCardStatement;
    }

    /**
     * Create a new credit card statement.
     *
     * @param  string  $bookId
     * @param  string  $outline
     * @param  string|null  $memo
     * @param  string  $date
     * @param  int|null  $displayOrder
     * @return string
     */
    public function createCreditCardStatement($bookId, $outline, $memo, $date, $displayOrder)
    {
        $creditCardStatementId = $this->creditCardStatement->create($bookId, $outline, $memo, $date, $displayOrder);

        return $creditCardStatementId;
    }

    /**
     * Delete the credit card statement.
     *
     * @param  string  $creditCardStatementId
     * @return void
     */
    public function deleteCreditCardStatement($creditCardStatementId)
    {
        $this->creditCardStatement->delete($creditCardStatementId);
    }

    /**
     * Retrieve a list of credit card statements of the book.
     *
     * @param  string  $bookId
     * @param  string|null  $creditCardStatementId
     * @return array<string, array{
     *   credit_card_statement_id: string,
     *   credit_card_statement_outline: string,
     *   credit_card_statement_memo: string|null,
     *   date: string,
     * }>
     */
    public function retrieveCreditCardStatements($bookId, $creditCardStatementId)
    {
        $creditCardStatements = [];

        /** @var array<int, array{
         *  credit_card_statement_id: string,
         *  credit_card_statement_outline: string,
         *  credit_card_statement_memo: string|null,
         *  date: string
         * }> $creditCardStatementList */
        $creditCardStatementList = $this->creditCardStatement->searchBook($bookId, $creditCardStatementId);
        foreach ($creditCardStatementList as $creditCardStatementItem) {
            $creditCardStatements[strval($creditCardStatementItem['credit_card_statement_id'])] = [
                'credit_card_statement_id' => strval($creditCardStatementItem['credit_card_statement_id']),
                'credit_card_statement_outline' => strval($creditCardStatementItem['credit_card_statement_outline']),
                'credit_card_statement_memo' => strval($creditCardStatementItem['credit_card_statement_memo']),
                'date' => strval($creditCardStatementItem['date']),
            ];
        }

        return $creditCardStatements;
    }

    /**
     * Update the credit card statement.
     *
     * @param  string  $creditCardStatementId
     * @param  array{
     *   outline?: string,
     *   memo?: string,
     *   date?: string,
     * }  $newData
     * @return void
     */
    public function updateCreditCardStatement($creditCardStatementId, array $newData)
    {
        $this->creditCardStatement->update($creditCardStatementId, $newData);
    }
}
