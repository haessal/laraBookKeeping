<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\CreditCardStatementRepositoryInterface;
use App\Models\CreditCardStatement;

class CreditCardStatementRepository implements CreditCardStatementRepositoryInterface
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
    public function create($bookId, $outline, $memo, $date, $displayOrder)
    {
        $creditCardStatement = new CreditCardStatement();
        $creditCardStatement->book_id = $bookId;
        $creditCardStatement->credit_card_statement_outline = $outline;
        $creditCardStatement->credit_card_statement_memo = $memo;
        $creditCardStatement->date = $date;
        $creditCardStatement->display_order = $displayOrder;
        $creditCardStatement->save();

        return $creditCardStatement->credit_card_statement_id;
    }

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
    public function createForImporting(array $newCreditCardStatement)
    {
        $creditCardStatement = new CreditCardStatement();
        $creditCardStatement->credit_card_statement_id = $newCreditCardStatement['credit_card_statement_id'];
        $creditCardStatement->book_id = $newCreditCardStatement['book_id'];
        $creditCardStatement->credit_card_statement_outline = $newCreditCardStatement['credit_card_statement_outline'];
        $creditCardStatement->credit_card_statement_memo = $newCreditCardStatement['credit_card_statement_memo'];
        $creditCardStatement->date = $newCreditCardStatement['date'];
        $creditCardStatement->display_order = $newCreditCardStatement['display_order'];
        $creditCardStatement->save();
        $creditCardStatement->refresh();
        if ($newCreditCardStatement['deleted']) {
            $creditCardStatement->delete();
        }
    }

    /**
     * Delete the credit card statement.
     *
     * @param  string  $creditCardStatementId
     * @return void
     */
    public function delete($creditCardStatementId)
    {
        /** @var \App\Models\CreditCardStatement|null $creditCardStatement */
        $creditCardStatement = CreditCardStatement::query()->find($creditCardStatementId);
        if (! is_null($creditCardStatement)) {
            $creditCardStatement->delete();
        }
    }

    /**
     * Search the book for credit card statement.
     *
     * @param  string  $bookId
     * @param  string|null  $creditCardStatementId
     * @return array<int, array<string, mixed>>
     */
    public function searchBook($bookId, $creditCardStatementId): array
    {
        /** @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query */
        $query = CreditCardStatement::query()
            ->select(
                'credit_card_statement_id',
                'credit_card_statement_outline',
                'credit_card_statement_memo',
                'date',
            )
            ->where('book_id', $bookId)
            ->whereNull('deleted_at');
        if (isset($creditCardStatementId)) {
            $query = $query->where('credit_card_statement_id', $creditCardStatementId);
        }
        /** @var array<int, array<string, mixed>> $list */
        $list = $query->orderBy('date')->get()->toArray();

        return $list;
    }

    /**
     * Search the book for credit card statements to export.
     *
     * @param  string  $bookId
     * @param  string|null  $creditCardStatementId
     * @return array<int, array<string, mixed>>
     */
    public function searchBookForExporting($bookId, $creditCardStatementId = null): array
    {
        /** @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query */
        $query = CreditCardStatement::withTrashed()
            ->select('*')
            ->where('book_id', $bookId);
        if (isset($creditCardStatementId)) {
            $query = $query->where('credit_card_statement_id', $creditCardStatementId);
        }
        /** @var array<int, array<string, mixed>> $list */
        $list = $query->get()->toArray();

        return $list;
    }

    /**
     * Update the credit card statement.
     *
     * @param  string  $creditCardStatementId
     * @param  array<string, string>  $newData
     * @return void
     */
    public function update($creditCardStatementId, array $newData)
    {
        /** @var \App\Models\CreditCardStatement|null $creditCardStatement */
        $creditCardStatement = CreditCardStatement::query()->find($creditCardStatementId);
        if (! is_null($creditCardStatement)) {
            if (array_key_exists('outline', $newData)) {
                $creditCardStatement->credit_card_statement_outline = $newData['outline'];
            }
            if (array_key_exists('memo', $newData)) {
                $creditCardStatement->credit_card_statement_memo = $newData['memo'];
            }
            if (array_key_exists('date', $newData)) {
                $creditCardStatement->date = $newData['date'];
            }
            $creditCardStatement->save();
        }
    }

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
    public function updateForImporting(array $newCreditCardStatement)
    {
        /** @var \App\Models\CreditCardStatement|null $creditCardStatement */
        $creditCardStatement = CreditCardStatement::withTrashed()->find($newCreditCardStatement['credit_card_statement_id']);
        if (! is_null($creditCardStatement)) {
            $creditCardStatement->book_id = $newCreditCardStatement['book_id'];
            $creditCardStatement->credit_card_statement_outline = $newCreditCardStatement['credit_card_statement_outline'];
            $creditCardStatement->credit_card_statement_memo = $newCreditCardStatement['credit_card_statement_memo'];
            $creditCardStatement->date = $newCreditCardStatement['date'];
            $creditCardStatement->display_order = $newCreditCardStatement['display_order'];
            $creditCardStatement->touch();
            $creditCardStatement->save();
            $creditCardStatement->refresh();
            if ($creditCardStatement->trashed()) {
                if (! $newCreditCardStatement['deleted']) {
                    $creditCardStatement->restore();
                }
            } else {
                if ($newCreditCardStatement['deleted']) {
                    $creditCardStatement->delete();
                }
            }
        }
    }
}
