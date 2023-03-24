<?php

namespace Tests\Unit\DataProvider\BookRepositoryInterface;

use App\DataProvider\Eloquent\BookRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

    public function test_it_takes_one_argument_and_returns_an_array_or_null(): void
    {
        $bookId = '3274cc74-99a1-47f4-aa57-66da432f5dad';

        $book = $this->book->findById($bookId);

        if (is_null($book)) {
            $this->assertTrue(is_null($book));
        } else {
            $this->assertTrue(is_array($book));
        }
    }
}
