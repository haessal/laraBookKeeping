<?php

namespace App\Service;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;

class BookService
{
    /**
     * Book repository instance.
     *
     * @var \App\DataProvider\BookRepositoryInterface
     */
    private $book;

    /**
     * Permission repository instance.
     *
     * @var \App\DataProvider\PermissionRepositoryInterface
     */
    private $permission;

    /**
     * Create a new BookService instance.
     *
     * @param  \App\DataProvider\BookRepositoryInterface  $book
     * @param  \App\DataProvider\PermissionRepositoryInterface  $app
     */
    public function __construct(BookRepositoryInterface $book, PermissionRepositoryInterface $permission)
    {
        $this->book = $book;
        $this->permission = $permission;
    }

    /**
     * Create new Book.
     *
     * @param  int  $userId
     * @param  string  $title
     * @return string $bookId
     */
    public function createBook(int $userId, string $title): string
    {
        $bookId = $this->book->create($title);
        $this->permission->create($userId, $bookId, true, true, false);

        return $bookId;
    }

    /**
     * Owner of the specified Book.
     *
     * @param  int  $bookId
     * @return string | null
     */
    public function ownerName(string $bookId): ?string
    {
        $ownerName = null;
        $user = $this->permission->findOwnerOfBook($bookId);
        if (! empty($user)) {
            $ownerName = $user['name'];
        }

        return $ownerName;
    }

    /**
     * Retrieve a Book.
     *
     * @param  string  $bookId
     * @param  int  $userId
     * @return array | null
     */
    public function retrieveBook(string $bookId, int $userId): ?array
    {
        $booklist = $this->permission->searchBookList($userId, $bookId);
        if (count($booklist) == 1) {
            $book = $booklist[0];
        } else {
            $book = null;
        }

        return $book;
    }

    /**
     * Retrieve list of accessable Book.
     *
     * @param  int  $userId
     * @return array
     */
    public function retrieveBookList(int $userId): array
    {
        $booklist = $this->permission->searchBookList($userId);

        return $booklist;
    }

    /**
     * Retrieve default Book.
     *
     * @param  int  $userId
     * @return string | null
     */
    public function retrieveDefaultBook(int $userId)
    {
        $bookId = $this->permission->findDefaultBook($userId);

        return $bookId;
    }

    /**
     * Retrieve information.
     *
     * @param  string  $bookId
     * @return array | null
     */
    public function retrieveInformation(string $bookId): ?array
    {
        $book = $this->book->findById($bookId);

        return $book;
    }
}
