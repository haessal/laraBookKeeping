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
    public function createForImporting($newBook)
    {
        $book = new Book();
        $book->book_id = $newBook['book_id'];
        $book->book_name = $newBook['book_name'];
        $book->display_order = $newBook['display_order'];
        $book->save();
        $book->refresh();
        if ($newBook['deleted']) {
            $book->delete();
        }
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
    public function updateForImporting($newBook)
    {
        /** @var \App\Models\Book|null $book */
        $book = Book::withTrashed()->find($newBook['book_id']);
        if (! is_null($book)) {
            $book->book_name = $newBook['book_name'];
            $book->display_order = $newBook['display_order'];
            $book->touch();
            $book->save();
            $book->refresh();
            if ($book->trashed()) {
                if (! $newBook['deleted']) {
                    $book->restore();
                }
            } else {
                if ($newBook['deleted']) {
                    $book->delete();
                }
            }
        }
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
