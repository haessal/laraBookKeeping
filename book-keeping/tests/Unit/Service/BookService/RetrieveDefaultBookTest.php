<?php

namespace Tests\Unit\Service\BookService;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;
use App\Service\BookService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveDefaultBookTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_retrieve_the_default_book_of_the_user(): void
    {
        $userId = 1;
        $bookId_expected = (string) Str::uuid();
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldReceive('findDefaultBook')
            ->once()
            ->with($userId)
            ->andReturn($bookId_expected);

        $book = new BookService($bookMock, $permissionMock);
        $bookId_actual = $book->retrieveDefaultBook($userId);

        $this->assertSame($bookId_expected, $bookId_actual);
    }
}
