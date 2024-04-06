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
     * Create a new account group to import.
     *
     * @param  array{
     *   account_group_id: string,
     *   book_id: string,
     *   account_type: string,
     *   account_group_title: string,
     *   bk_uid: int|null,
     *   account_group_bk_code: int|null,
     *   is_current: bool,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }  $newAccountGroup
     * @return void
     */
    public function createForImporting(array $newAccountGroup)
    {
        $accountGroup = new CreditCardStatement();
        $accountGroup->account_group_id = $newAccountGroup['account_group_id'];
        $accountGroup->book_id = $newAccountGroup['book_id'];
        $accountGroup->account_type = $newAccountGroup['account_type'];
        $accountGroup->account_group_title = $newAccountGroup['account_group_title'];
        $accountGroup->bk_uid = $newAccountGroup['bk_uid'];
        $accountGroup->account_group_bk_code = $newAccountGroup['account_group_bk_code'];
        $accountGroup->is_current = $newAccountGroup['is_current'];
        $accountGroup->display_order = $newAccountGroup['display_order'];
        $accountGroup->save();
        $accountGroup->refresh();
        if ($newAccountGroup['deleted']) {
            $accountGroup->delete();
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
     * Search the book for account groups to export.
     *
     * @param  string  $bookId
     * @param  string|null  $accountGroupId
     * @return array<int, array<string, mixed>>
     */
    public function searchBookForExporting($bookId, $accountGroupId = null): array
    {
        /** @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query */
        $query = CreditCardStatement::withTrashed()
            ->select('*')
            ->where('book_id', $bookId);
        if (isset($accountGroupId)) {
            $query = $query->where('account_group_id', $accountGroupId);
        }
        /** @var array<int, array<string, mixed>> $list */
        $list = $query->get()->toArray();

        return $list;
    }

    /**
     * Update the account group.
     *
     * @param  string  $accountGroupId
     * @param  array<string, mixed>  $newData
     * @return void
     */
    public function update($accountGroupId, array $newData)
    {
        /** @var \App\Models\CreditCardStatement|null $accountGroup */
        $accountGroup = CreditCardStatement::query()->find($accountGroupId);
        if (! is_null($accountGroup)) {
            if (array_key_exists('title', $newData)) {
                $accountGroup->account_group_title = strval($newData['title']);
            }
            if (array_key_exists('is_current', $newData)) {
                $accountGroup->is_current = boolval($newData['is_current']);
            }
            $accountGroup->save();
        }
    }

    /**
     * Update the account group to import.
     *
     * @param  array{
     *   account_group_id: string,
     *   book_id: string,
     *   account_type: string,
     *   account_group_title: string,
     *   bk_uid: int|null,
     *   account_group_bk_code: int|null,
     *   is_current: bool,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }  $newAccountGroup
     * @return void
     */
    public function updateForImporting(array $newAccountGroup)
    {
        /** @var \App\Models\CreditCardStatement|null $accountGroup */
        $accountGroup = CreditCardStatement::withTrashed()->find($newAccountGroup['account_group_id']);
        if (! is_null($accountGroup)) {
            $accountGroup->book_id = $newAccountGroup['book_id'];
            $accountGroup->account_type = $newAccountGroup['account_type'];
            $accountGroup->account_group_title = $newAccountGroup['account_group_title'];
            $accountGroup->bk_uid = $newAccountGroup['bk_uid'];
            $accountGroup->account_group_bk_code = $newAccountGroup['account_group_bk_code'];
            $accountGroup->is_current = $newAccountGroup['is_current'];
            $accountGroup->display_order = $newAccountGroup['display_order'];
            $accountGroup->touch();
            $accountGroup->save();
            $accountGroup->refresh();
            if ($accountGroup->trashed()) {
                if (! $newAccountGroup['deleted']) {
                    $accountGroup->restore();
                }
            } else {
                if ($newAccountGroup['deleted']) {
                    $accountGroup->delete();
                }
            }
        }
    }
}
