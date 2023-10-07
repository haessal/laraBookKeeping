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

class RetrieveBookInformationTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_does_nothing_because_the_specified_book_is_not_readable(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 25;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckReadable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, '']);
        $bookMock->shouldNotReceive('retrieveOwnerNameOf');
        $bookMock->shouldNotReceive('retrieveInformationOf');
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->retrieveBookInformation($bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_retrieves_nothing_because_the_owner_of_the_book_is_not_found(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 54;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $bookName = 'bookName795';
        $book = ['book_id' => $bookId, 'book_name' => $bookName];
        $result_expected = [BookKeepingService::STATUS_NORMAL, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckReadable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, $bookId]);
        $bookMock->shouldReceive('retrieveOwnerNameOf')
            ->once()
            ->with($bookId)
            ->andReturn(null);
        $bookMock->shouldReceive('retrieveInformationOf')
            ->once()
            ->with($bookId)
            ->andReturn($book);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->retrieveBookInformation($bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_retrieves_the_information_of_the_book(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 91;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $owner = 'owner826';
        $bookName = 'bookName827';
        $book = ['book_id' => $bookId, 'book_name' => $bookName];
        $information = ['id' => $bookId, 'owner' => $owner, 'name' => $bookName];
        $result_expected = [BookKeepingService::STATUS_NORMAL, $information];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckReadable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, $bookId]);
        $bookMock->shouldReceive('retrieveOwnerNameOf')
            ->once()
            ->with($bookId)
            ->andReturn($owner);
        $bookMock->shouldReceive('retrieveInformationOf')
            ->once()
            ->with($bookId)
            ->andReturn($book);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->retrieveBookInformation($bookId);

        $this->assertSame($result_expected, $result_actual);
    }
}
