<?php

namespace App\DataProvider;

interface AccountGroupRepositoryInterface
{
    /**
     * Create new account group.
     *
     * @param string $bookId
     * @param string $accountType
     * @param string $title
     * @param bool   $isCurrent
     * @param int    $bk_uid
     * @param int    $bk_code
     *
     * @return string $accountGroupId
     */
    public function create(string $bookId, string $accountType, string $title, bool $isCurrent, $bk_uid, $bk_code): string;

    /**
     * Search account group.
     *
     * @param string $bookId
     *
     * @return array
     */
    public function search(string $bookId): array;

    /**
     * Search account group for export.
     *
     * @param string $bookId
     * @param string|null $accountGroupId
     *
     * @return array
     */
    public function searchForExport(string $bookId, string $accountGroupId = null): array;

    /**
     * Update account group.
     *
     * @param string $accountGroupId
     * @param array  $newData
     */
    public function update(string $accountGroupId, array $newData);
}
