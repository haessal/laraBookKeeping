<?php

namespace App\DataProvider;

interface AccountRepositoryInterface
{
    /**
     * Create new account.
     *
     * @param string $accountGroupId
     * @param string $title
     * @param string $description
     * @param int    $bk_uid
     * @param int    $bk_code
     *
     * @return string $accountId
     */
    public function create(string $accountGroupId, string $title, string $description, $bk_uid, $bk_code): string;

    /**
     * Search account.
     *
     * @param string $bookId
     *
     * @return array
     */
    public function searchAccount(string $bookId): array;

    /**
     * Search account for export with accout group id.
     *
     * @param string $accountGroupId
     *
     * @return array
     */
    public function searchAccountForExport(string $accountGroupId): array;

    /**
     * Update account.
     *
     * @param string $accountId
     * @param array  $newData
     */
    public function update(string $accountId, array $newData);
}
