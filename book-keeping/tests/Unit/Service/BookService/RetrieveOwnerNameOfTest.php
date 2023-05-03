<?php

namespace Tests\Unit\Service\BookService;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;
use App\Service\BookService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveOwnerNameOfTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_retrieve_the_name_of_the_owner_of_the_book(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 56;
        $ownerName_expected = 'user56';
        $user = ['id' => $userId, 'name' => $ownerName_expected];
        /** @var \App\DataProvider\BookRepositoryInterface|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        /** @var \App\DataProvider\PermissionRepositoryInterface|\Mockery\MockInterface $permissionMock */
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldReceive('findOwnerOfBook')
            ->once()
            ->with($bookId)
            ->andReturn($user);
        $book = new BookService($bookMock, $permissionMock);
        $ownerName_actual = $book->retrieveOwnerNameOf($bookId);

        $this->assertSame($ownerName_expected, $ownerName_actual);
    }
}
