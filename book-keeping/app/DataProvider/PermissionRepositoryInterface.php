<?php

namespace App\DataProvider;

interface PermissionRepositoryInterface
{
    /**
     * Create a permission for the user to modify the book.
     *
     * If the permission is the first one for the user, the book is mark as the
     * default book for the user. And If no user has permission to access the
     * book yet, the user is registered as the owner of the book.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @return string
     */
    public function create(int $userId, string $bookId): string;

    /**
     * Find the books that the user can access.
     *
     * @param  int  $userId
     * @return array<int, array<string, string>>
     */
    public function findAccessibleBooks(int $userId): array;

    /**
     * Find the default book of the user.
     *
     * @param  int  $userId
     * @return string|null
     */
    public function findDefaultBook(int $userId): ?string;

    /**
     * Find the owner of the book.
     *
     * @param  string  $bookId
     * @return array<string, string>|null
     */
    public function findOwnerOfBook(string $bookId): ?array;

    /**
     * Search book list that the user can access.(TODO: merge with findAccessibleBooks)
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @return array
     */
    public function searchBookList(int $userId, string $bookId = null): array;
}
