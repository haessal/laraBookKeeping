<?php

namespace Tests\Unit\Service\BookService;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;
use App\Service\BookService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class UpdateDefaultMarkOfTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_update_the_mark_for_indicating_that_the_book_is_default_one(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 222;
        $isDefault = true;
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldReceive('updateDefaultBookMark')
            ->once()
            ->with($userId, $bookId, $isDefault);
        $book = new BookService($bookMock, $permissionMock);
        $book->updateDefaultMarkOf($bookId, $userId, $isDefault);

        $this->assertTrue(true);
    }
}
