<?php

namespace App\DataProvider;

interface AccountGroupRepositoryInterface
{
    /**
     * Create a new account group to be bound in the book.
     *
     * @param  string  $bookId
     * @param  string  $accountType
     * @param  string  $title
     * @param  bool  $isCurrent
     * @param  int|null  $bk_uid
     * @param  int|null  $bk_code
     * @return string
     */
    public function create($bookId, $accountType, $title, $isCurrent, $bk_uid, $bk_code);

    /**
     * Search the book for account groups.
     *
     * @param  string  $bookId
     * @return array<int, array<string, mixed>>
     */
    public function searchBook($bookId): array;

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
}
