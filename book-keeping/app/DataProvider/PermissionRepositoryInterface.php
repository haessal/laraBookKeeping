<?php

namespace App\DataProvider;

interface PermissionRepositoryInterface
{
    /**
     * Create a new permission for the user to access the book.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @param  bool  $modifiable
     * @param  bool  $is_owner
     * @param  bool  $is_default
     * @return string
     */
    public function create($userId, $bookId, $modifiable, $is_owner, $is_default);

    /**
     * Delete the user's permission to access the book.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @return void
     */
    public function delete($userId, $bookId);

    /**
     * Find the book that the user can access.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @return array<string, string>|null
     */
    public function findBook($userId, $bookId): ?array;

    /**
     * Find the permission to access the book.
     *
     * @param  string  $bookId
     * @return array<int, array<string, mixed>>
     */
    public function findByBookId($bookId): array;

    /**
     * Find the default book of the user.
     *
     * @param  int  $userId
     * @return string|null
     */
    public function findDefaultBook($userId);

    /**
     * Find the owner of the book.
     *
     * @param  string  $bookId
     * @return array<string, string>|null
     */
    public function findOwnerOfBook($bookId): ?array;

    /**
     * Find the user.
     *
     * @param  int  $userId
     * @return array<string, mixed>|null
     */
    public function findUser($userId): ?array;

    /**
     * Find the user by his/her name.
     *
     * @param  string  $name
     * @return array<string, mixed>|null
     */
    public function findUserByName($name): ?array;

    /**
     * Search for the available books.
     *
     * @param  int  $userId
     * @return array<int, array<string, string>>
     */
    public function searchForAccessibleBooks($userId): array;

    /**
     * Update the mark for indicating that the book is default one for the user.
     *
     * @param  int  $userId
     * @param  string  $bookId
     * @param  bool  $isDefault
     * @return void
     */
    public function updateDefaultBookMark($userId, $bookId, $isDefault);
}
