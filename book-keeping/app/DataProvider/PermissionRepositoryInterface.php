<?php

namespace App\DataProvider;

interface PermissionRepositoryInterface
{
    /**
     * Create a permission for the user to access the book.
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
     * Delete the user's permission to access the book.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @return void
     */
    public function delete(int $userId, string $bookId): void;

    /**
     * Find the book that the user can access.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @return array<string, string>|null
     */
    public function findBook(int $userId, string $bookId): ?array;

    /**
     * Find the permission to access the book.
     *
     * @param  string  $bookId
     * @return array<int, array<string, mixed>>
     */
    public function findByBookId(string $bookId): array;

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
     * Find the user.
     *
     * @param  int  $userId
     * @return array<string, string>|null
     */
    public function findUser(int $userId): ?array;

    /**
     * Find the user by his/her name.
     *
     * @param  string  $name
     * @return array<string, string>|null
     */
    public function findUserByName(string $name): ?array;

    /**
     * Search for the available books.
     *
     * @param  int  $userId
     * @return array<int, array<string, string>>
     */
    public function searchForAccessibleBooks(int $userId): array;

    /**
     * Update the mark for indicating that the book is default one for the user.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @param  bool  $isDefault
     * @return void
     */
    public function updateDefaultBookMark(int $userId, string $bookId, bool $isDefault): void;
}
