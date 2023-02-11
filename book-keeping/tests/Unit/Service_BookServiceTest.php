<?php

namespace Tests\Unit;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;
use App\Service\BookService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Service_BookServiceTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function createBook_CreateBookAndRegistPermissionByCallRepository()
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

    /**
     * @test
     */
    public function ownerName_FindOwnerName()
    {
        $userId = 56;
        $ownerName_expected = 'user56';
        $user = ['id' => $userId, 'name' => $ownerName_expected];
        $bookId = (string) Str::uuid();
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldReceive('findOwnerOfBook')
            ->once()
            ->with($bookId)
            ->andReturn($user);
        $book = new BookService($bookMock, $permissionMock);
        $ownerName_actual = $book->ownerName($bookId);

        $this->assertSame($ownerName_expected, $ownerName_actual);
    }

    /**
     * @test
     */
    public function ownerName_NoOwnerFound()
    {
        $bookId = (string) Str::uuid();
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldReceive('findOwnerOfBook')
            ->once()
            ->with($bookId)
            ->andReturn(null);
        $book = new BookService($bookMock, $permissionMock);
        $ownerName = $book->ownerName($bookId);

        $this->assertNull($ownerName);
    }

    /**
     * @test
     */
    public function retrieveBookList_CallRepositoryWithArgumentsAsItIs()
    {
        $userId = 1;
        $booklist_expected = [
            ['book_id' => (string) Str::uuid(), 'book_name' => 'book1', 'modifiable' => true, 'is_owner' => true, 'is_default' => true, 'created_at' => new Carbon()],
            ['book_id' => (string) Str::uuid(), 'book_name' => 'book2', 'modifiable' => true, 'is_owner' => false, 'is_default' => false, 'created_at' => new Carbon()],
        ];
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldReceive('searchForAccessibleBooks')
            ->once()
            ->with($userId)
            ->andReturn($booklist_expected);

        $book = new BookService($bookMock, $permissionMock);
        $booklist_actual = $book->retrieveBookList($userId);

        $this->assertSame($booklist_expected, $booklist_actual);
    }

    /**
     * @test
     */
    public function retrieveDefaultBook_CallRepositoryWithArgumentsAsItIs()
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

    /**
     * @test
     */
    public function retrieveInformation_CallRepositoryWithArgumentsAsItIs()
    {
        $bookId = (string) Str::uuid();
        $book_expected = ['book_id' => $bookId, 'book_name' => 'bookName160'];
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        $bookMock->shouldReceive('findById')
            ->once()
            ->with($bookId)
            ->andReturn($book_expected);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);

        $book = new BookService($bookMock, $permissionMock);
        $book_actual = $book->retrieveInformation($bookId);

        $this->assertSame($book_expected, $book_actual);
    }
}
