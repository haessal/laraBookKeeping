<?php

namespace Tests\Unit\Service\BookService;

use App\Repositories\BookRepositoryInterface;
use App\Repositories\PermissionRepositoryInterface;
use App\Service\BookService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class UpdateNameOfTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_calls_repository_to_update_the_name_of_the_book(): void
    {
        $bookId = (string) Str::uuid();
        $newName = 'newBookName23';
        /** @var \App\Repositories\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        $bookMock->shouldReceive('updateName')
            ->once()
            ->with($bookId, $newName);
        /** @var \App\Repositories\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);

        $book = new BookService($bookMock, $permissionMock);
        $book->updateNameOf($bookId, $newName);

        $this->assertTrue(true);
    }
}
