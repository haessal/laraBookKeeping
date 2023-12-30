<?php

namespace Tests\Unit\Service\BookKeepingService;

use App\Service\AccountService;
use App\Service\BookKeepingService;
use App\Service\BookService;
use App\Service\BudgetService;
use App\Service\SlipService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveBookTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_retrieves_the_book(): void
    {
        $bookId = (string) Str::uuid();
        $bookName = 'BookName29';
        $userId = 25;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $bookItem = [
            'book_id' => $bookId,
            'book_name' => $bookName,
            'modifiable' => false,
            'is_owner' => false,
            'is_default' => false,
            'created_at' => '2023-05-02 19:18:01',
        ];
        $userNameOfOwner = 'owner37';
        $book_expected = [
            'id' => $bookId,
            'name' => $bookName,
            'is_default' => false,
            'is_owner' => false,
            'modifiable' => false,
            'owner' => $userNameOfOwner,
        ];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveBook')
            ->once()
            ->with($bookId, $userId)
            ->andReturn($bookItem);
        $bookMock->shouldReceive('retrieveOwnerNameOf')
            ->once()
            ->with($bookId)
            ->andReturn($userNameOfOwner);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $book_actual = $BookKeeping->retrieveBook($bookId);

        $this->assertSame($book_expected, $book_actual);
    }
}
