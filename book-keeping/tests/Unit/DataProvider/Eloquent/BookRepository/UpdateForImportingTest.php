<?php

namespace Tests\Unit\DataProvider\Eloquent\BookRepository;

use App\DataProvider\Eloquent\BookRepository;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateForImportingTest extends TestCase
{
    use RefreshDatabase;

    protected $book;

    public function setUp(): void
    {
        parent::setUp();
        $this->book = new BookRepository();
    }

    public function test_one_record_is_updated(): void
    {
        $bookName = 'name24';
        $displayOrder = 1;
        $deleted = false;
        $bookId = Book::factory()->create([
            'book_name' => 'name28',
            'display_order' => 2,
        ])->book_id;
        $newBook = [
            'book_id' => $bookId,
            'book_name' => $bookName,
            'display_order' => $displayOrder,
            'deleted' => $deleted,
        ];

        $this->book->updateForImporting($newBook);

        $this->assertDatabaseHas('bk2_0_books', [
            'book_id' => $bookId,
            'book_name' => $bookName,
            'display_order' => $displayOrder,
            'deleted_at' => null,
        ]);
    }

    public function test_one_record_is_updated_and_then_deleted(): void
    {
        $bookName = 'name50';
        $displayOrder = 1;
        $deleted = true;
        $bookId = Book::factory()->create([
            'book_name' => 'name54',
            'display_order' => 2,
        ])->book_id;
        $newBook = [
            'book_id' => $bookId,
            'book_name' => $bookName,
            'display_order' => $displayOrder,
            'deleted' => $deleted,
        ];

        $this->book->updateForImporting($newBook);

        $this->assertSoftDeleted('bk2_0_books', [
            'book_id' => $bookId,
            'book_name' => $bookName,
            'display_order' => $displayOrder,
        ]);
    }

    public function test_one_record_is_updated_and_then_restored(): void
    {
        $bookName = 'name75';
        $displayOrder = 1;
        $deleted = false;
        $book = Book::factory()->create([
            'book_name' => 'name79',
            'display_order' => 2,
        ]);
        $bookId = $book->book_id;
        $book->delete();
        $newBook = [
            'book_id' => $bookId,
            'book_name' => $bookName,
            'display_order' => $displayOrder,
            'deleted' => $deleted,
        ];

        $this->book->updateForImporting($newBook);

        $this->assertDatabaseHas('bk2_0_books', [
            'book_id' => $bookId,
            'book_name' => $bookName,
            'display_order' => $displayOrder,
            'deleted_at' => null,
        ]);
    }

    public function test_one_record_is_updated_and_still_in_the_trash(): void
    {
        $bookName = 'name103';
        $displayOrder = 1;
        $deleted = true;
        $book = Book::factory()->create([
            'book_name' => 'name107',
            'display_order' => 2,
        ]);
        $bookId = $book->book_id;
        $book->delete();
        $newBook = [
            'book_id' => $bookId,
            'book_name' => $bookName,
            'display_order' => $displayOrder,
            'deleted' => $deleted,
        ];

        $this->book->updateForImporting($newBook);

        $this->assertSoftDeleted('bk2_0_books', [
            'book_id' => $bookId,
            'book_name' => $bookName,
            'display_order' => $displayOrder,
        ]);
    }
}
