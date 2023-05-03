<?php

namespace Tests\Unit\Service\BookKeepingService;

use App\Service\AccountService;
use App\Service\BookKeepingService;
use App\Service\BookService;
use App\Service\BudgetService;
use App\Service\SlipService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveBookInformationTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_retrieves_nothing_because_the_information_of_the_book_is_not_found(): void
    {
        $bookId = (string) Str::uuid();
        $owner = 'owner765';
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveOwnerNameOf')
            ->once()
            ->with($bookId)
            ->andReturn($owner);
        $bookMock->shouldReceive('retrieveInformationOf')
            ->once()
            ->with($bookId)
            ->andReturn(null);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $information = $BookKeeping->retrieveBookInformation($bookId);

        $this->assertNull($information);
    }

    public function test_it_retrieves_nothing_because_the_owner_of_the_book_is_not_found(): void
    {
        $bookId = (string) Str::uuid();
        $bookName = 'bookName795';
        $book = ['book_id' => $bookId, 'book_name' => $bookName];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
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
        $information = $BookKeeping->retrieveBookInformation($bookId);

        $this->assertNull($information);
    }

    public function test_it_retrieves_the_information_of_the_book(): void
    {
        $bookId = (string) Str::uuid();
        $owner = 'owner826';
        $bookName = 'bookName827';
        $book = ['book_id' => $bookId, 'book_name' => $bookName];
        $information_expected = ['id' => $bookId, 'owner' => $owner, 'name' => $bookName];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
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
        $information_actual = $BookKeeping->retrieveBookInformation($bookId);

        $this->assertSame($information_expected, $information_actual);
    }
}
