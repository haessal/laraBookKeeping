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
     * Find the book.
     *
     * @param  string  $bookId
     * @return array<string, string>|null
     */
    public function findById($bookId): ?array;

    /**
     * Update the name of the book.
     *
     * @param  string  $bookId
     * @param  string  $newName
     * @return void
     */
    public function updateName($bookId, $newName);
}
