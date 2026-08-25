<?php

namespace Tests\Unit\Repositories\Eloquent\BookRepository;

use App\Models\DataProvider\Eloquent\Book;
use App\Repositories\Eloquent\BookRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FindByIdForExportingTest extends TestCase
{
    use RefreshDatabase;

    /** @var BookRepository */
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
