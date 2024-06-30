<?php

namespace Tests\Unit\Service\BookKeepingService;

use App\Service\AccountService;
use App\Service\BookKeepingService;
use App\Service\BookService;
use App\Service\BudgetService;
use App\Service\CreditCardStatementService;
use App\Service\SlipService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class CreateCreditCardStatementTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_creates_a_new_create_credit_card_statement(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 26;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $outline = 'outline30';
        $memo = 'memo31';
        $date = '2024-07-07';
        $displayOrder = 32;
        $creditCardStatementId = (string) Str::uuid();
        $result_expected = [BookKeepingService::STATUS_NORMAL, $creditCardStatementId];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, $bookId]);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        /** @var \App\Service\CreditCardStatementService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementService::class);
        $creditCardStatementMock->shouldReceive('createCreditCardStatement')
            ->once()
            ->with($bookId, $outline, $memo, $date, $displayOrder)
            ->andReturn($creditCardStatementId);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock, $creditCardStatementMock);
        $result_actual = $BookKeeping->createCreditCardStatement($outline, $memo, $date, $displayOrder, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_specified_book_is_not_writable(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 59;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $outline = 'outline68';
        $memo = 'memo69';
        $date = '2024-07-07';
        $displayOrder = 72;
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN, '']);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        /** @var \App\Service\CreditCardStatementService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementService::class);
        $creditCardStatementMock->shouldNotReceive('createCreditCardStatement');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock, $creditCardStatementMock);
        $result_actual = $BookKeeping->createCreditCardStatement($outline, $memo, $date, $displayOrder, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }
}
