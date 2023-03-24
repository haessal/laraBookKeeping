<?php

namespace Tests\Unit\DataProvider\Eloquent\BookRepository;

use App\DataProvider\Eloquent\BookRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
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
        $title = 'string';

        $bookId = $this->book->create($title);

        $this->assertDatabaseHas('bk2_0_books', [
            'book_id'   => $bookId,
            'book_name' => $title,
        ]);
    }
}
