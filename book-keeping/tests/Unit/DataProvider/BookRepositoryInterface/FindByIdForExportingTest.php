<?php

namespace Tests\Unit\DataProvider\BookRepositoryInterface;

use App\DataProvider\Eloquent\BookRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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

    public function test_it_takes_one_argument_and_returns_an_array_or_null(): void
    {
        $bookId = (string) Str::uuid();

        $book = $this->book->findByIdForExporting($bookId);

        if (is_null($book)) {
            $this->assertTrue(is_null($book));
        } else {
            $this->assertTrue(is_array($book));
        }
    }
}
