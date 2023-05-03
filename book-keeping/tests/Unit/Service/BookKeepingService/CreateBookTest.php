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

class CreateBookTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_creates_a_new_book(): void
    {
        $userId = 24;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $title = 'BookTitle28';
        $bookId_expected = (string) Str::uuid();
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('createBook')
            ->once()
            ->with($userId, $title)
            ->andReturn($bookId_expected);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $bookId_actual = $BookKeeping->createBook($title);

        $this->assertSame($bookId_expected, $bookId_actual);
    }
}
