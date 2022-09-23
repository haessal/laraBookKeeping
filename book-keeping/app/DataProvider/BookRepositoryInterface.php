<?php

namespace App\DataProvider;

interface BookRepositoryInterface
{
    /**
     * Create a book.
     *
     * @param  string  $title
     * @return string
     */
    public function create(string $title): string;

    /**
     * Find the book.
     *
     * @param  string  $bookId
     * @return array<string, string>|null
     */
    public function findById(string $bookId): ?array;

    /**
     * Update the name.
     *
     * @param  string  $bookId
     * @param  string  $newName
     */
    public function updateName(string $bookId, string $newName);
}
