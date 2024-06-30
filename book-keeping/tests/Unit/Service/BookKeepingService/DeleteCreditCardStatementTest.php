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

class DeleteCreditCardStatementTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_deletes_the_credit_card_statement(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 255;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $creditCardStatementId = (string) Str::uuid();
        $result_expected = [BookKeepingService::STATUS_NORMAL, null];
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
        $slipMock->shouldReceive('retrieveSlipEntriesRegisteredInCreditCardStatement')
            ->once()
            ->with($bookId, $creditCardStatementId)
            ->andReturn([]);
        /** @var \App\Service\CreditCardStatementService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementService::class);
        $creditCardStatementMock->shouldReceive('retrieveCreditCardStatements')
            ->once()
            ->with($bookId, $creditCardStatementId)
            ->andReturn([
                'credit_card_statement_id' => $creditCardStatementId,
                'credit_card_statement_outline' => 'outline65',
                'credit_card_statement_memo' => 'memo66',
                'date' => '2024-07-07',
            ]);
        $creditCardStatementMock->shouldReceive('deleteCreditCardStatement')
            ->once()
            ->with($creditCardStatementId);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock, $creditCardStatementMock);
        $result_actual = $BookKeeping->deleteCreditCardStatement($creditCardStatementId, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_some_slip_entries_are_registered_in_the_credit_card_statement_to_be_deleted(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 91;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $creditCardStatementId = (string) Str::uuid();
        $result_expected = [BookKeepingService::STATUS_ERROR_BAD_CONDITION, null];
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
        $slipMock->shouldReceive('retrieveSlipEntriesRegisteredInCreditCardStatement')
            ->once()
            ->with($bookId, $creditCardStatementId)
            ->andReturn([
                [
                    'slip_entry_id' => (string) Str::uuid(),
                    'slip_id' => (string) Str::uuid(),
                    'debit' => (string) Str::uuid(),
                    'credit' => (string) Str::uuid(),
                    'amount' => 990,
                    'client' => 'client100',
                    'outline' => 'outline101',
                    'credit_card_statement_id' => $creditCardStatementId,
                ],
            ]);
        /** @var \App\Service\CreditCardStatementService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementService::class);
        $creditCardStatementMock->shouldReceive('retrieveCreditCardStatements')
            ->once()
            ->with($bookId, $creditCardStatementId)
            ->andReturn([
                'credit_card_statement_id' => $creditCardStatementId,
                'credit_card_statement_outline' => 'outline112',
                'credit_card_statement_memo' => 'memo113',
                'date' => '2024-06-07',
            ]);
        $creditCardStatementMock->shouldNotReceive('deleteCreditCardStatement');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock, $creditCardStatementMock);
        $result_actual = $BookKeeping->deleteCreditCardStatement($creditCardStatementId, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_credit_card_statement_to_be_deleted_is_not_found(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 146;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $creditCardStatementId = (string) Str::uuid();
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, null];
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
        $slipMock->shouldNotReceive('retrieveSlipEntriesRegisteredInCreditCardStatement');
        /** @var \App\Service\CreditCardStatementService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementService::class);
        $creditCardStatementMock->shouldReceive('retrieveCreditCardStatements')
            ->once()
            ->with($bookId, $creditCardStatementId)
            ->andReturn([]);
        $creditCardStatementMock->shouldNotReceive('deleteCreditCardStatement');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock, $creditCardStatementMock);
        $result_actual = $BookKeeping->deleteCreditCardStatement($creditCardStatementId, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_specified_book_is_not_writable(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 186;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $creditCardStatementId = (string) Str::uuid();
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN, $bookId]);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldNotReceive('retrieveSlipEntriesRegisteredInCreditCardStatement');
        /** @var \App\Service\CreditCardStatementService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementService::class);
        $creditCardStatementMock->shouldNotReceive('retrieveCreditCardStatements');
        $creditCardStatementMock->shouldNotReceive('deleteCreditCardStatement');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock, $creditCardStatementMock);
        $result_actual = $BookKeeping->deleteCreditCardStatement($creditCardStatementId, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }
}
