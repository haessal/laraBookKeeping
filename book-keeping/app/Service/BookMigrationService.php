<?php

namespace App\Service;

use App\Repositories\BookRepositoryInterface;
use App\Repositories\PermissionRepositoryInterface;

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
     * @param  \App\Repositories\BookRepositoryInterface  $book
     * @param  \App\Repositories\PermissionRepositoryInterface  $permission
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
            /** @var array{
             *   book_id: string,
             *   book_name: string,
             *   display_order: int|null,
             *   updated_at: string|null,
             *   deleted: bool,
             * } $converted
             */
            $converted = $this->tools->convertExportedTimestamps($book);
        } else {
            $converted = null;
        }

        return $converted;
    }
}
