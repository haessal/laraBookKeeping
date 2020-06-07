<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\BookRepositoryInterface;

class BookRepository implements BookRepositoryInterface
{
    /**
     * Create new book and regist the user as its owner.
     *
     * @param string $title
     *
     * @return string $bookId
     */
    public function create(string $title): string
    {
        $book = new Book();
        $book->book_name = $title;
        $book->save();

        return $book->book_id;
    }

    /**
     * Find book.
     *
     * @param string $bookId
     *
     * @return array | null
     */
    public function findById(string $bookId): ?array
    {
        $book = Book::select('book_id', 'book_name')
            ->where('book_id', $bookId)
            ->first();

        return is_null($book) ? null : $book->toArray();
    }
}
