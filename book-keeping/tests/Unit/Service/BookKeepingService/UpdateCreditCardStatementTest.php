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

class UpdateCreditCardStatementTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_updates_the_credit_card_statement(): void
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
        $newData = [
            'outline' => 'outline44',
            'memo' => 'memo55',
            'date' => '2024-07-06',
        ];
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
        /** @var \App\Service\CreditCardStatementService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementService::class);
        $creditCardStatementMock->shouldReceive('retrieveCreditCardStatements')
            ->once()
            ->with($bookId, $creditCardStatementId)
            ->andReturn($creditCardStatements);
        $creditCardStatementMock->shouldReceive('updateCreditCardStatement')
            ->once()
            ->with($creditCardStatementId, $newData);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock, $creditCardStatementMock);
        $result_actual = $BookKeeping->updateCreditCardStatement($creditCardStatementId, $newData, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_specified_credit_card_statement_is_not_found_in_the_book(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 80;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $creditCardStatementId = (string) Str::uuid();
        $newData = [
            'outline' => 'outline86',
            'memo' => 'memo87',
            'date' => '2024-07-08',
        ];
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
        /** @var \App\Service\CreditCardStatementService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementService::class);
        $creditCardStatementMock->shouldReceive('retrieveCreditCardStatements')
            ->once()
            ->with($bookId, $creditCardStatementId)
            ->andReturn([]);
        $creditCardStatementMock->shouldNotReceive('updateCreditCardStatement');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock, $creditCardStatementMock);
        $result_actual = $BookKeeping->updateCreditCardStatement($creditCardStatementId, $newData, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_specified_book_is_not_writable(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 20;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $creditCardStatementId = (string) Str::uuid();
        $newData = [
            'outline' => 'outline26',
            'memo' => 'memo27',
            'date' => '2024-07-28',
        ];
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
        $creditCardStatementMock->shouldNotReceive('retrieveCreditCardStatements');
        $creditCardStatementMock->shouldNotReceive('updateCreditCardStatement');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock, $creditCardStatementMock);
        $result_actual = $BookKeeping->updateCreditCardStatement($creditCardStatementId, $newData, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }
}
