<?php

namespace App\DataProvider;

interface AccountGroupRepositoryInterface
{
    /**
     * Create an account group to be bound in the book.
     *
     * @param  string  $bookId
     * @param  string  $accountType
     * @param  string  $title
     * @param  bool  $isCurrent
     * @param  int|null  $bk_uid
     * @param  int|null  $bk_code
     * @return string
     */
    public function create(string $bookId, string $accountType, string $title, bool $isCurrent, ?int $bk_uid, ?int $bk_code): string;

    /**
     * Search the book for account groups.
     *
     * @param  string  $bookId
     * @return array<int, array<string, mixed>>
     */
    public function searchBook(string $bookId): array;

    /**
     * Update the account group.
     *
     * @param  string  $accountGroupId
     * @param  array<string, mixed>  $newData
     * @return void
     */
    public function update(string $accountGroupId, array $newData): void;
}
