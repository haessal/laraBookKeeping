<?php

namespace App\DataProvider;

interface PermissionRepositoryInterface
{
    /**
     * Create new permission.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @param  bool  $modifiable
     * @param  bool  $is_owner
     * @param  bool  $is_default
     * @return string $permissionId
     */
    public function create(int $userId, string $bookId, bool $modifiable, bool $is_owner, bool $is_default): string;

    /**
     * Delete the permission.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @return void
     */
    public function delete(int $userId, string $bookId);

    /**
     * Find default book of the user.
     *
     * @param  int  $userId
     * @return string | null
     */
    public function findDefaultBook(int $userId);

    /**
     * Find owner of the book.
     *
     * @param  string  $bookId
     * @return array | null
     */
    public function findOwnerOfBook(string $bookId): ?array;

    /**
     * Find user.
     *
     * @param  int  $userId
     * @return array | null
     */
    public function findUser(int $userId): ?array;

    /**
     * Find user by name.
     *
     * @param  string  $name
     * @return array | null
     */
    public function findUserByName(string $name): ?array;

    /**
     * Search book list that the user can access.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @return array
     */
    public function searchBookList(int $userId, string $bookId = null): array;

    /**
     * Search permission list for the book.
     *
     * @param  string  $bookId
     * @return array
     */
    public function searchPermissionList(string $bookId): array;

    /**
     * Update the flag which indicates that the book is default one.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @param  int  $isDefault
     */
    public function updateBookIsDefault(int $userId, string $bookId, bool $isDefault);
}
