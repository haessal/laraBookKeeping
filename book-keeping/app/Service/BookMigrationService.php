<?php

namespace App\Service;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;

class BookMigrationService extends BookService
{
    /**
     * BookKeeping migration tools instance.
     *
     * @var \App\Service\BookKeepingMigrationTools
     */
    protected $tools;

    /**
     * Create a new BookMigrationService instance.
     *
     * @param  \App\DataProvider\BookRepositoryInterface  $book
     * @param  \App\DataProvider\PermissionRepositoryInterface  $permission
     * @param  \App\Service\BookKeepingMigrationTools  $tools
     */
    public function __construct(BookRepositoryInterface $book, PermissionRepositoryInterface $permission, BookKeepingMigrationTools $tools)
    {
        parent::__construct($book, $permission);
        $this->tools = $tools;
    }

    /**
     * Export information.
     *
     * @param  string  $bookId
     * @return array{
     *   book_id: string,
     *   book_name: string,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }|null
     */
    public function exportInformation($bookId): ?array
    {
        /** @var array{
         *   book_id: string,
         *   book_name: string,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }|null $book
         */
        $book = $this->book->findByIdForExporting($bookId);
        if (isset($book)) {
            $converted = [
                'book_id' => $book['book_id'],
                'book_name' => $book['book_name'],
                'display_order' => $book['display_order'],
                'updated_at' => $book['updated_at'],
                'deleted' => ! is_null($book['deleted_at']),
            ];
        } else {
            $converted = null;
        }

        return $converted;
    }
}
