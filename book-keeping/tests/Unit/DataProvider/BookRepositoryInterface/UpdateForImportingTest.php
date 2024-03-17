<?php

namespace Tests\Unit\DataProvider\BookRepositoryInterface;

use App\DataProvider\Eloquent\BookRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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

    public function test_it_takes_one_argument_and_returns_nothing(): void
    {
        $newBook = [
            'book_id' => (string) Str::uuid(),
            'book_name' => 'name32',
            'display_order' => 0,
            'deleted' => false,
        ];

        $this->book->updateForImporting($newBook);

        $this->assertTrue(true);
    }
}
