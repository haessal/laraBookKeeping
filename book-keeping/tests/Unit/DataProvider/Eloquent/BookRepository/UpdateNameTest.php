<?php

namespace Tests\Unit\DataProvider\Eloquent\BookRepository;

use App\DataProvider\Eloquent\BookRepository;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateNameTest extends TestCase
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
        $name = 'bookName57';
        $newName = 'bookNewName58';
        $bookId = Book::factory()->create([
            'book_name' => $name,
        ])->book_id;

        $this->book->updateName($bookId, $newName);

        $this->assertDatabaseHas('bk2_0_books', [
            'book_id'   => $bookId,
            'book_name' => $newName,
        ]);
    }
}
