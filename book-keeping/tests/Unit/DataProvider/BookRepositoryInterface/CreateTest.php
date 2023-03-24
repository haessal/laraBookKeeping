<?php

namespace Tests\Unit\DataProvider\BookRepositoryInterface;

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

    public function test_it_takes_one_argument_and_returns_a_value_of_type_string(): void
    {
        $title = 'string';

        $bookId = $this->book->create($title);

        $this->assertTrue(is_string($bookId));
    }
}
