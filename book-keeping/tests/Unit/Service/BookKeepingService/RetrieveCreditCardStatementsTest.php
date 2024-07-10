<?php

namespace Tests\Unit\Service\BookKeepingService;

use App\Service\AccountService;
use App\Service\BookKeepingService;
use App\Service\BookService;
use App\Service\BudgetService;
use App\Service\CreditCardStatementService;
use App\Service\SlipService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveCreditCardStatementsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_retrieves_a_list_of_credit_card_statements(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 127;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $creditCardStatementId = (string) Str::uuid();
        $creditCardStatementOutlone = 'outline32';
        $creditCardStatementMemo = 'memo33';
        $creditCardStatementDate = '2024-07-04';
        $creditCardStatements = [
            $creditCardStatementId => [
                'credit_card_statement_id' => $creditCardStatementId,
                'credit_card_statement_outline' => $creditCardStatementOutlone,
                'credit_card_statement_memo' => $creditCardStatementMemo,
                'date' => $creditCardStatementDate,
            ],
        ];
        $result_expected = [BookKeepingService::STATUS_NORMAL, $creditCardStatements];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckReadable')
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
        $creditCardStatementMock->shouldReceive('retrieveCreditCardStatements')
            ->once()
            ->with($bookId, $creditCardStatementId)
            ->andReturn($creditCardStatements);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock, $creditCardStatementMock);
        $result_actual = $BookKeeping->retrieveCreditCardStatements($bookId, $creditCardStatementId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_specified_book_is_not_readable(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 72;
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
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        /** @var \App\Service\CreditCardStatementService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementService::class);
        $creditCardStatementMock->shouldNotReceive('retrieveCreditCardStatements');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock, $creditCardStatementMock);
        $result_actual = $BookKeeping->retrieveCreditCardStatements($bookId);

        $this->assertSame($result_expected, $result_actual);
    }
}
