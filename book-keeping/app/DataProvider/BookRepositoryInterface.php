<?php

namespace App\DataProvider;

interface BookRepositoryInterface
{
    /**
     * Create a new book.
     *
     * @param  string  $title
     * @return string
     */
    public function create($title);

    /**
     * Create a new book to import.
     *
     * @param  array{
     *   book_id: string,
     *   book_name: string,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }  $newBook
     * @return void
     */
    public function createForImporting($newBook);

    /**
     * Find the book.
     *
     * @param  string  $bookId
     * @return array<string, string>|null
     */
    public function findById($bookId): ?array;

    /**
     * Find the book to export.
     *
     * @param  string  $bookId
     * @return array<string, mixed>|null
     */
    public function findByIdForExporting($bookId): ?array;

    /**
     * Update the book to import.
     *
     * @param  array{
     *   book_id: string,
     *   book_name: string,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }  $newBook
     * @return void
     */
    public function updateForImporting($newBook);

    /**
     * Update the name of the book.
     *
     * @param  string  $bookId
     * @param  string  $newName
     * @return void
     */
    public function updateName($bookId, $newName);
}
