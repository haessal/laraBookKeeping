<?php

namespace Tests\Unit\Service\BookKeepingService;

use App\Service\AccountService;
use App\Service\BookKeepingService;
use App\Service\BookService;
use App\Service\BudgetService;
use App\Service\SlipService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveAvailableBooksTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        Carbon::setTestNow();
    }

    public function test_it_retrieves_a_list_of_available_books(): void
    {
        $userId = 723;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $ownerName = 'owner727';
        $bookId = (string) Str::uuid();
        $bookName = 'book734';
        $bookList = [
            ['book_id' => $bookId, 'book_name' => $bookName, 'modifiable' => true, 'is_owner' => false, 'is_default' => false, 'created_at' => new Carbon()],
        ];
        $books_expected = [
            ['id' => $bookId, 'name' => $bookName, 'is_default' => false, 'is_owner' => false, 'modifiable' => true, 'owner' => $ownerName],
        ];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveBooks')
            ->once()
            ->with($userId)
            ->andReturn($bookList);
        $bookMock->shouldReceive('retrieveOwnerNameOf')
            ->once()
            ->with($bookId)
            ->andReturn($ownerName);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $books_actual = $BookKeeping->retrieveAvailableBooks();

        $this->assertSame($books_expected, $books_actual);
    }
}
