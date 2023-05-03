<?php

namespace Tests\Unit\Service\BookService;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;
use App\Service\BookService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class CreateBookTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_create_a_new_book(): void
    {
        $userId = 1;
        $title = 'title';
        $modifiable = true;
        $is_owner = true;
        $is_default = false;
        $bookId_expected = (string) Str::uuid();
        $permissionId = (string) Str::uuid();
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        $bookMock->shouldReceive('create')
            ->once()
            ->with($title)
            ->andReturn($bookId_expected);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldReceive('create')
            ->once()
            ->with($userId, $bookId_expected, $modifiable, $is_owner, $is_default)
            ->andReturn($permissionId);

        $book = new BookService($bookMock, $permissionMock);
        $bookId_actual = $book->createBook($userId, $title);

        $this->assertSame($bookId_expected, $bookId_actual);
    }
}
