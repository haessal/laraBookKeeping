<?php

namespace App\Service;

use App\Repositories\CreditCardStatementRepositoryInterface;

class CreditCardStatementMigrationService extends CreditCardStatementService
{
    /**
     * BookKeeping migration tools instance.
     *
     * @var \App\Service\BookKeepingMigrationTools
     */
    protected $tools;

    /**
     * Create a new CreditCardStatementMigrationService instance.
     *
     * @param  \App\Repositories\CreditCardStatementRepositoryInterface  $creditCardStatement
     * @param  \App\Service\BookKeepingMigrationTools  $tools
     */
    public function __construct(CreditCardStatementRepositoryInterface $creditCardStatement, BookKeepingMigrationTools $tools)
    {
        parent::__construct($creditCardStatement);
        $this->tools = $tools;
    }

    /**
     * Dump credit card statements of the book.
     *
     * @param  string  $bookId
     * @return array{
     *   credit_card_statement_id: string,
     *   book_id: string,
     *   credit_card_statement_outline: string,
     *   credit_card_statement_memo: string|null,
     *   date: string,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }[]
     */
    public function dumpCreditCardStatements($bookId): array
    {
        $creditCardStatements = [];

        /** @var array{
         *   credit_card_statement_id: string,
         *   book_id: string,
         *   credit_card_statement_outline: string,
         *   credit_card_statement_memo: string|null,
         *   date: string,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $creditCardStatementList
         */
        $creditCardStatementList = $this->creditCardStatement->searchBookForExporting($bookId);
        foreach ($creditCardStatementList as $creditCardStatement) {
            /** @var array{
             *   credit_card_statement_id: string,
             *   book_id: string,
             *   credit_card_statement_outline: string,
             *   credit_card_statement_memo: string|null,
             *   date: string,
             *   display_order: int|null,
             *   updated_at: string|null,
             *   deleted: bool,
             * } $convertedCreditCardStatement
             */
            $convertedCreditCardStatement = $this->tools->convertExportedTimestamps($creditCardStatement);
            $creditCardStatements[] = $convertedCreditCardStatement;
        }

        return $creditCardStatements;
    }

    /**
     * Export credit card statements of the book.
     *
     * @param  string  $bookId
     * @return array<string, array{
     *   credit_card_statement_id: string,
     *   updated_at: string|null,
     * }>
     */
    public function exportCreditCardStatements($bookId): array
    {
        $creditCardStatements = [];

        /** @var array{
         *   credit_card_statement_id: string,
         *   book_id: string,
         *   credit_card_statement_outline: string,
         *   credit_card_statement_memo: string|null,
         *   date: string,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $creditCardStatementList
         */
        $creditCardStatementList = $this->creditCardStatement->searchBookForExporting($bookId);
        foreach ($creditCardStatementList as $creditCardStatement) {
            $creditCardStatementId = $creditCardStatement['credit_card_statement_id'];
            $creditCardStatements[$creditCardStatementId] = [
                'credit_card_statement_id' => $creditCardStatement['credit_card_statement_id'],
                'updated_at' => $this->tools->convertExportedTimestamp($creditCardStatement['updated_at']),
            ];
        }

        return $creditCardStatements;
    }
}
