<?php

namespace App\DataProvider;

interface PermissionRepositoryInterface
{
    /**
     * Create a permission for the user to modify the book.(TODO: Update the description)
     *
     * If the permission is the first one for the user, the book is mark as the
     * default book for the user. And If no user has permission to access the
     * book yet, the user is registered as the owner of the book.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @param  bool  $modifiable
     * @param  bool  $is_owner
     * @param  bool  $is_default
     * @return string
     */
    public function create(int $userId, string $bookId, bool $modifiable, bool $is_owner, bool $is_default): string;

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

    /**
     * Update the flag which indicates that the book is default one.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @param  int  $isDefault
     */
    public function updateBookIsDefault(int $userId, string $bookId, bool $isDefault);
}
