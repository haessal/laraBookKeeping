<?php

namespace App\Service;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;
use App\DataProvider\UserRepositoryInterface;
use Illuminate\Support\Carbon;

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
     * User repository instance.
     *
     * @var \App\DataProvider\UserRepositoryInterface
     */
    private $user;

    /**
     * Create a new BookService instance.
     *
     * @param \App\DataProvider\BookRepositoryInterface       $book
     * @param \App\DataProvider\PermissionRepositoryInterface $app
     * @param \App\DataProvider\UserRepositoryInterface       $user
     */
    public function __construct(BookRepositoryInterface $book, PermissionRepositoryInterface $permission, UserRepositoryInterface $user)
    {
        $this->book = $book;
        $this->permission = $permission;
        $this->user = $user;
    }

    /**
     * Create new Book.
     *
     * @param int    $userId
     * @param string $title
     *
     * @return string $bookId
     */
    public function createBook(int $userId, string $title): string
    {
        $bookId = $this->book->create($title);
        $permissionId = $this->permission->create($userId, $bookId);

        return $bookId;
    }

    /**
     * Export information.
     *
     * @param string $bookId
     *
     * @return array | null
     */
    public function exportInformation(string $bookId): ?array
    {
        $book = $this->book->findByIdForExport($bookId);
        if (isset($book)) {
            $converted = [];
            foreach ($book as $key => $value) {
                switch ($key) {
                    case 'created_at':
                        break;
                    case 'updated_at':
                        $d = Carbon::createFromFormat('Y-m-d H:i:s', $value);
                        $converted['updated_at'] = $d->toAtomString();
                        break;
                    case 'deleted_at':
                        $converted['deleted'] = !is_null($value);
                        break;
                    default:
                        $converted[$key] = $value;
                        break;
                }
            }
        } else {
            $converted = null;
        }

        return $converted;
    }

    /**
     * Owner of the specified Book.
     *
     * @param int $bookId
     *
     * @return string | null
     */
    public function ownerName(string $bookId): ?string
    {
        $ownerName = null;
        $userId = $this->permission->findOwnerOfBook($bookId);
        if (!empty($userId)) {
            $user = $this->user->findById($userId);
            $ownerName = $user['name'];
        }

        return $ownerName;
    }

    /**
     * Retrieve list of accessable Book.
     *
     * @param int $userId
     *
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
     * @param int $userId
     *
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
     * @param string $bookId
     *
     * @return array | null
     */
    public function retrieveInformation(string $bookId): ?array
    {
        $book = $this->book->findById($bookId);

        return $book;
    }
}
