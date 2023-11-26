<?php

namespace Tests\Unit\DataProvider\Eloquent\BookRepository;

use App\DataProvider\Eloquent\BookRepository;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FindByIdForExportingTest extends TestCase
{
    use RefreshDatabase;

    protected $book;

    public function setUp(): void
    {
        parent::setUp();
        $this->book = new BookRepository();
    }

    public function test_it_returns_the_exported_book(): void
    {
        $bookName = 'name25';
        $displayOrder = 1;
        $bookId = Book::factory()->create([
            'book_name' => $bookName,
            'display_order' => $displayOrder,
        ])->book_id;

        $book = $this->book->findByIdForExporting($bookId);

        $this->assertSame([
            'book_id',
            'book_name',
            'display_order',
            'created_at',
            'updated_at',
            'deleted_at',
        ], array_keys($book));
    }
}
