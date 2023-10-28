<?php

namespace App\DataProvider;

interface AccountRepositoryInterface
{
    /**
     * Create a new account to be bound in the account group.
     *
     * @param  string  $accountGroupId
     * @param  string  $title
     * @param  string  $description
     * @param  int|null  $bk_uid
     * @param  int|null  $bk_code
     * @return string
     */
    public function create($accountGroupId, $title, $description, $bk_uid, $bk_code);

    /**
     * Create a new account to import.
     *
     * @param  array{
     *   account_id: string,
     *   account_group_id: string,
     *   account_title: string,
     *   description: string,
     *   selectable: bool,
     *   bk_uid: int|null,
     *   account_bk_code: int|null,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }  $newAccount
     * @return void
     */
    public function createForImporting(array $newAccount);

    /**
     * Search the account group for account items to export.
     *
     * @param  string  $accountGroupId
     * @param  string|null  $accountId
     * @return array<int, array<string, mixed>>
     */
    public function searchAccountGropupForExporting($accountGroupId, $accountId = null): array;

    /**
     * Search the book for accounts.
     *
     * @param  string  $bookId
     * @return array<int, array<string, mixed>>
     */
    public function searchBook($bookId): array;

    /**
     * Update the account.
     *
     * @param  string  $accountId
     * @param  array<string, mixed>  $newData
     * @return void
     */
    public function update($accountId, array $newData);

    /**
     * Update the account to import.
     *
     * @param  array{
     *   account_id: string,
     *   account_group_id: string,
     *   account_title: string,
     *   description: string,
     *   selectable: bool,
     *   bk_uid: int|null,
     *   account_bk_code: int|null,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }  $newAccount
     * @return void
     */
    public function updateForImporting(array $newAccount);
}
