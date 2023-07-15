<?php

namespace Tests\Unit\Service\BookService;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;
use App\Service\BookKeepingService;
use App\Service\BookService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveDefaultBookOrCheckWritableTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_retrieves_the_default_book(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 2024;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $result_expected = [BookKeepingService::STATUS_NORMAL, $bookId];
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldReceive('findDefaultBook')
            ->once()
            ->with($userId)
            ->andReturn($bookId);
        $permissionMock->shouldNotReceive('findBook');

        $book = new BookService($bookMock, $permissionMock);
        $result_actual = $book->retrieveDefaultBookOrCheckWritable(null, $userId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_cant_not_find_the_default_book(): void
    {
        $userId = 2047;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, ''];
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldReceive('findDefaultBook')
            ->once()
            ->with($userId)
            ->andReturn(null);
        $permissionMock->shouldNotReceive('findBook');

        $book = new BookService($bookMock, $permissionMock);
        $result_actual = $book->retrieveDefaultBookOrCheckWritable(null, $userId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_determins_the_specified_book_is_writable(): void
    {
        $bookId = (string) Str::uuid();
        $book = ['modifiable' => true];
        $userId = 2072;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $result_expected = [BookKeepingService::STATUS_NORMAL, $bookId];
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldNotReceive('findDefaultBook');
        $permissionMock->shouldReceive('findBook')
            ->once()
            ->with($userId, $bookId)
            ->andReturn($book);

        $book = new BookService($bookMock, $permissionMock);
        $result_actual = $book->retrieveDefaultBookOrCheckWritable($bookId, $userId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_determins_the_specified_book_is_not_writable(): void
    {
        $bookId = (string) Str::uuid();
        $book = ['modifiable' => false];
        $userId = 2097;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN, ''];
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldNotReceive('findDefaultBook');
        $permissionMock->shouldReceive('findBook')
            ->once()
            ->with($userId, $bookId)
            ->andReturn($book);

        $book = new BookService($bookMock, $permissionMock);
        $result_actual = $book->retrieveDefaultBookOrCheckWritable($bookId, $userId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_can_not_find_the_specified_book(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 121;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, ''];
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldNotReceive('findDefaultBook');
        $permissionMock->shouldReceive('findBook')
            ->once()
            ->with($userId, $bookId)
            ->andReturn(null);

        $book = new BookService($bookMock, $permissionMock);
        $result_actual = $book->retrieveDefaultBookOrCheckWritable($bookId, $userId);

        $this->assertSame($result_expected, $result_actual);
    }
}
