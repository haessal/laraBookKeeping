<?php

namespace Tests\Unit\DataProvider\BookRepositoryInterface;

use App\DataProvider\Eloquent\BookRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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

    public function test_it_takes_two_arguments_and_returns_nothing(): void
    {
        $bookId = (string) Str::uuid();
        $newName = 'string';

        $this->book->updateName($bookId, $newName);

        $this->assertTrue(true);
    }
}
