<?php

namespace Tests\Unit\Repositories\Eloquent\BookRepository;

use App\Models\DataProvider\Eloquent\Book;
use App\Repositories\Eloquent\BookRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FindByIdTest extends TestCase
{
    use RefreshDatabase;

    protected $book;

    public function setUp(): void
    {
        parent::setUp();
        $this->book = new BookRepository();
    }

    public function test_it_returns_the_book(): void
    {
        $name = 'bookName48';
        $bookId = Book::factory()->create([
            'book_name' => $name,
        ])->book_id;
        $book_expected = ['book_id' => $bookId, 'book_name' => $name];

        $book_actual = $this->book->findById($bookId);

        $this->assertSame($book_expected, $book_actual);
    }
}
