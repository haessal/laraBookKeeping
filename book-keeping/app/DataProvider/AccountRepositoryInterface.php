<?php

namespace App\DataProvider;

interface AccountRepositoryInterface
{
    /**
     * Create an account to be bound in the account group.
     *
     * @param  string  $accountGroupId
     * @param  string  $title
     * @param  string  $description
     * @param  int|null  $bk_uid
     * @param  int|null  $bk_code
     * @return string
     */
    public function create(string $accountGroupId, string $title, string $description, ?int $bk_uid, ?int $bk_code): string;

    /**
     * Search the book for accounts.
     *
     * @param  string  $bookId
     * @return array<int, array<string, mixed>>
     */
    public function searchBook(string $bookId): array;

    /**
     * Update the account.
     *
     * @param  string  $accountId
     * @param  array<string, mixed>  $newData
     * @return void
     */
    public function update(string $accountId, array $newData): void;
}
