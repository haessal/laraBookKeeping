<?php

namespace Tests\Unit\Service\BookService;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;
use App\Service\BookService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveBooksTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_retrieve_a_list_of_books_that_is_available_to_the_user(): void
    {
        $userId = 1;
        $booklist_expected = [
            [
                'book_id' => (string) Str::uuid(),
                'book_name' => 'book1',
                'modifiable' => true,
                'is_owner' => true,
                'is_default' => true,
                'created_at' => '2023-05-01 10:53:42',
            ],
            [
                'book_id' => (string) Str::uuid(),
                'book_name' => 'book2',
                'modifiable' => true,
                'is_owner' => false,
                'is_default' => false,
                'created_at' => '2023-05-01 10:43:42',
            ],
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
        $booklist_actual = $book->retrieveBooks($userId);

        $this->assertSame($booklist_expected, $booklist_actual);
    }
}
