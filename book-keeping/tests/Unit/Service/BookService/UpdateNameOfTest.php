<?php

namespace Tests\Unit\Service\BookService;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;
use App\Service\BookService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class UpdateNameOfTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_update_the_name_of_the_book(): void
    {
        $bookId = (string) Str::uuid();
        $newName = 'newBookName23';
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        $bookMock->shouldReceive('updateName')
            ->once()
            ->with($bookId, $newName);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);

        $book = new BookService($bookMock, $permissionMock);
        $book->updateNameOf($bookId, $newName);

        $this->assertTrue(true);
    }
}
