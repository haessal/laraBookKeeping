<?php

namespace Tests\Unit\Service\BookService;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;
use App\Service\BookService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class CreatePermissionTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_create_a_new_permission(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 23;
        $userName = 'user24';
        $mode = 'ReadWrite';
        $permission_expected = ['user' => $userName, 'permitted_to' => $mode];
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldReceive('findUserByName')
            ->once()
            ->with($userName)
            ->andReturn(['id' => $userId, 'name' => $userName]);
        $permissionMock->shouldReceive('create')
            ->once()
            ->with($userId, $bookId, true, false, false);

        $book = new BookService($bookMock, $permissionMock);
        $permission_actual = $book->createPermission($bookId, $userName, $mode);

        $this->assertSame($permission_expected, $permission_actual);
    }
}
