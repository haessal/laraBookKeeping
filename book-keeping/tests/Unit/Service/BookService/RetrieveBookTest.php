<?php

namespace Tests\Unit\Service\BookService;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;
use App\Service\BookService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveBookTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_retrieve_the_book(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 23;
        $book_expected = [
            'book_id' => $bookId,
            'book_name' => 'BookName26',
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => true,
            'created_at' => '2023-05-03 16:21:20',
        ];
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldReceive('findBook')
            ->once()
            ->with($userId, $bookId)
            ->andReturn($book_expected);

        $book = new BookService($bookMock, $permissionMock);
        $book_actual = $book->retrieveBook($bookId, $userId);

        $this->assertSame($book_expected, $book_actual);
    }
}
