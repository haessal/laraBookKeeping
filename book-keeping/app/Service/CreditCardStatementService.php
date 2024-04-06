<?php

namespace App\Service;

use App\DataProvider\CreditCardStatementRepositoryInterface;
use PhpParser\Node\Expr\FuncCall;

class CreditCardStatementService
{
    /**
     * Credit card statement repository instance.
     *
     * @var \App\DataProvider\CreditCardStatementRepositoryInterface
     */
    private $creditCardStatement;

    /**
     * Create a new CreditCardStatementService instance.
     *
     * @param  \App\DataProvider\CreditCardStatementRepositoryInterface  $creditCardStatement
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
     * Retrieve a list of credit card statements of the book.
     *
     * @param  string  $bookId
     * @param  string|null  $creditCardStatementId
     * @return array<string, array{
     *   credit_card_statement_id: string,
     *   credit_card_statement_outline: string,
     *   credit_card_statement_memo: string,
     *   date: string,
     * }>
     */
    public function retrieveCreditCardStatements($bookId, $creditCardStatementId)
    {
        $creditCardStatements = [];

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
}
