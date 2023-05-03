<?php

namespace Tests\Unit\Service\BookService;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;
use App\Service\BookService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrievePermissionsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_retrieve_a_list_of_permissions_related_to_the_book(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 23;
        $userName = 'user24';
        $permission_list = [
            ['permitted_user' => $userId, 'modifiable' => true, 'is_owner' => true, 'is_default' => true],
        ];
        $permissions_expected = [
            ['user' => $userName, 'permitted_to' => 'ReadWrite'],
        ];
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldReceive('findByBookId')
            ->once()
            ->with($bookId)
            ->andReturn($permission_list);
        $permissionMock->shouldReceive('findUser')
            ->once()
            ->with($userId)
            ->andReturn(['id' => $userId, 'name' => $userName]);

        $book = new BookService($bookMock, $permissionMock);
        $permissions_actual = $book->retrievePermissions($bookId);

        $this->assertSame($permissions_expected, $permissions_actual);
    }
}
