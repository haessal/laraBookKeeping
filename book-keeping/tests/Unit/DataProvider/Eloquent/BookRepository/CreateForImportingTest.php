<?php

namespace Tests\Unit\DataProvider\Eloquent\BookRepository;

use App\DataProvider\Eloquent\BookRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateForImportingTest extends TestCase
{
    use RefreshDatabase;

    protected $book;

    public function setUp(): void
    {
        parent::setUp();
        $this->book = new BookRepository();
    }

    public function test_one_record_is_created(): void
    {
        $bookId = (string) Str::uuid();
        $bookName = 'name26';
        $displayOrder = 0;
        $deleted = false;
        $newBook = [
            'book_id' => $bookId,
            'book_name' => $bookName,
            'display_order' => $displayOrder,
            'deleted' => $deleted,
        ];

        $this->book->createForImporting($newBook);

        $this->assertDatabaseHas('bk2_0_books', [
            'book_id' => $bookId,
            'book_name' => $bookName,
            'display_order' => $displayOrder,
            'deleted_at' => null,
        ]);
    }

    public function test_one_record_is_created_and_then_deleted(): void
    {
        $bookId = (string) Str::uuid();
        $bookName = 'name48';
        $displayOrder = 1;
        $deleted = true;
        $newBook = [
            'book_id' => $bookId,
            'book_name' => $bookName,
            'display_order' => $displayOrder,
            'deleted' => $deleted,
        ];

        $this->book->createForImporting($newBook);

        $this->assertSoftDeleted('bk2_0_books', [
            'book_id' => $bookId,
            'book_name' => $bookName,
            'display_order' => $displayOrder,
        ]);
    }
}
