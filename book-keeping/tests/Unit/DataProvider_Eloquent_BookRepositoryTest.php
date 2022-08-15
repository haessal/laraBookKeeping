<?php

namespace Tests\Unit;

use App\DataProvider\Eloquent\BookRepository;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DataProvider_Eloquent_BookRepositoryTest extends DataProvider_BookRepositoryInterfaceTest
{
    use RefreshDatabase;

    protected $book;

    public function setUp(): void
    {
        parent::setUp();
        $this->book = new BookRepository();
    }

    /**
     * @test
     */
    public function create_OneRecordIsCreated()
    {
        $title = 'string';

        $bookId = $this->book->create($title);

        $this->assertDatabaseHas('bk2_0_books', [
            'book_id'   => $bookId,
            'book_name' => $title,
        ]);
    }

    /**
     * @test
     */
    public function findById_ReturnOneBook()
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
