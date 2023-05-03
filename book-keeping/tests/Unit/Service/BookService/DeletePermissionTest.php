<?php

namespace Tests\Unit\Service\BookService;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;
use App\Service\BookService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class DeletePermissionTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_delete_the_permission_that_the_user_access_to_the_book(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 23;
        $userName = 'user24';
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldReceive('findUserByName')
            ->once()
            ->with($userName)
            ->andReturn(['id' => $userId, 'name' => $userName]);
        $permissionMock->shouldReceive('delete')
            ->once()
            ->with($userId, $bookId);

        $book = new BookService($bookMock, $permissionMock);
        $book->deletePermission($bookId, $userName);

        $this->assertTrue(true);
    }
}
