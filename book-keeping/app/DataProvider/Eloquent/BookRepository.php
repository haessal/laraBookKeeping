<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\BookRepositoryInterface;
use App\Models\Book;

class BookRepository implements BookRepositoryInterface
{
    /**
     * Create a new book.
     *
     * @param  string  $title
     * @return string
     */
    public function create($title)
    {
        $book = new Book();
        $book->book_name = $title;
        $book->save();

        return $book->book_id;
    }

    /**
     * Find the book.
     *
     * @param  string  $bookId
     * @return array<string, string>|null
     */
    public function findById($bookId): ?array
    {
        /** @var \Illuminate\Database\Eloquent\Model|null $book */
        $book = Book::query()->select('book_id', 'book_name')
            ->where('book_id', $bookId)
            ->first();

        return is_null($book) ? null : $book->toArray();
    }

    /**
     * Find the book to export.
     *
     * @param  string  $bookId
     * @return array<string, mixed>|null
     */
    public function findByIdForExporting($bookId): ?array
    {
        /** @var \Illuminate\Database\Eloquent\Model|null $book */
        $book = Book::query()
            ->select('*')
            ->where('book_id', $bookId)
            ->first();

        return is_null($book) ? null : $book->toArray();
    }

    /**
     * Update the name of the book.
     *
     * @param  string  $bookId
     * @param  string  $newName
     * @return void
     */
    public function updateName($bookId, $newName)
    {
        /** @var \App\Models\Book|null $book */
        $book = Book::query()->find($bookId);
        if (! is_null($book)) {
            $book->book_name = $newName;
            $book->save();
        }
    }
}
