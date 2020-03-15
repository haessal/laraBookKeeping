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
     * Find the account groups bound in the Book.
     *
     * @param string $bookId
     *
     * @return array
     */
    public function findAllByBoundIn(string $bookId): array;
}
