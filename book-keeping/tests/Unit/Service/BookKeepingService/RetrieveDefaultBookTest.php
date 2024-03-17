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

class RetrieveDefaultBookTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_retrieves_the_default_book(): void
    {
        $bookId = (string) Str::uuid();
        $bookName = 'BookName25';
        $userId = 26;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $bookItem = [
            'book_id' => $bookId,
            'book_name' => $bookName,
            'modifiable' => false,
            'is_owner' => false,
            'is_default' => false,
            'created_at' => '2023-05-02 19:50:01',
        ];
        $userNameOfOwner = 'owner37';
        $book = [
            'id' => $bookId,
            'name' => $bookName,
            'is_default' => false,
            'is_owner' => false,
            'modifiable' => false,
            'owner' => $userNameOfOwner,
        ];
        $result_expected = [BookKeepingService::STATUS_NORMAL, $book];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBook')
            ->once()
            ->with($userId)
            ->andReturn($bookId);
        $bookMock->shouldReceive('retrieveBook')  // call from $this->retrieveBook
            ->once()
            ->with($bookId, $userId)
            ->andReturn($bookItem);
        $bookMock->shouldReceive('retrieveOwnerNameOf')  // call from $this->retrieveBook
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
        $result_actual = $BookKeeping->retrieveDefaultBook();

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_retrieves_nothing_because_the_default_book_is_not_available(): void
    {
        $userId = 77;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBook')
            ->once()
            ->with($userId)
            ->andReturn(null);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->retrieveDefaultBook();

        $this->assertSame($result_expected, $result_actual);
    }
}
