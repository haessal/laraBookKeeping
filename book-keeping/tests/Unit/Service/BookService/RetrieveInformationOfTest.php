<?php

namespace Tests\Unit\Service\BookService;

use App\Repositories\BookRepositoryInterface;
use App\Repositories\PermissionRepositoryInterface;
use App\Service\BookService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveInformationOfTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_calls_repository_to_retrieve_the_information_of_the_book(): void
    {
        $bookId = (string) Str::uuid();
        $book_expected = ['book_id' => $bookId, 'book_name' => 'bookName160'];
        /** @var \App\Repositories\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        $bookMock->shouldReceive('findById')
            ->once()
            ->with($bookId)
            ->andReturn($book_expected);
        /** @var \App\Repositories\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);

        $book = new BookService($bookMock, $permissionMock);
        $book_actual = $book->retrieveInformationOf($bookId);

        $this->assertSame($book_expected, $book_actual);
    }
}
