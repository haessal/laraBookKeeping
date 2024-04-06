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
    public function createForImporting(array $newAccountGroup);

    /**
     * Search the book for credit card statement.
     *
     * @param  string  $bookId
     * @param  string|null  $creditCardStatementId
     * @return array<int, array<string, mixed>>
     */
    public function searchBook($bookId, $creditCardStatementId): array;

    /**
     * Search the book for account groups to export.
     *
     * @param  string  $bookId
     * @param  string|null  $accountGroupId
     * @return array<int, array<string, mixed>>
     */
    public function searchBookForExporting($bookId, $accountGroupId = null): array;

    /**
     * Update the account group.
     *
     * @param  string  $accountGroupId
     * @param  array<string, mixed>  $newData
     * @return void
     */
    public function update($accountGroupId, array $newData);

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
    public function updateForImporting(array $newAccountGroup);
}
