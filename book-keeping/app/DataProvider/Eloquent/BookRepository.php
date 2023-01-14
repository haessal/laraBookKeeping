<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\BookRepositoryInterface;
use App\Models\Book;

class BookRepository implements BookRepositoryInterface
{
    /**
     * Create a book.
     *
     * @param  string  $title
     * @return string
     */
    public function create(string $title): string
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
    public function findById(string $bookId): ?array
    {
        $book = Book::select('book_id', 'book_name')
            ->where('book_id', $bookId)
            ->first();

        return is_null($book) ? null : $book->toArray();
    }
}
