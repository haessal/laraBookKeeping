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

class RetrieveCreditCardStatementTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_retrieves_the_credit_card_statement(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 192;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $creditCardStatementId = (string) Str::uuid();
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accountId_3 = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntryId_2 = (string) Str::uuid();
        $amount_1 = 3700;
        $amount_2 = 380;
        $creditCardStatementOutlone = 'outline39';
        $creditCardStatementMemo = 'memo40';
        $creditCardStatementDate = '2024-07-31';
        $accounts = [
            $accountId_1 => [
                'account_type' => AccountService::ACCOUNT_TYPE_ASSET,
                'account_group_id' => $accountGroupId_1,
                'account_group_title' => 'accountGroupTitle_1',
                'is_current' => false,
                'account_id' => $accountId_1,
                'account_title' => 'accountTitle_1',
                'description' => 'description_1',
                'selectable' => true,
                'is_credit_card' => true,
                'account_bk_code' => 1201,
                'created_at' => '2019-12-03 12:00:01',
                'account_group_bk_code' => 1200,
                'account_group_created_at' => '2019-12-04 12:00:12',
            ],
        ];
        $slipEntry_1 = [
            'slip_id' => (string) Str::uuid(),
            'date' => '2024-07-01',
            'slip_outline' => 'client53',
            'slip_memo' => 'outline54',
            'slip_entry_id' => $slipEntryId_1,
            'debit' => $accountId_2,
            'credit' => $accountId_1,
            'amount' => $amount_1,
            'client' => 'client59',
            'outline' => 'outline60',
            'credit_card_statement_id' => $creditCardStatementId,
        ];
        $slipEntry_2 = [
            'slip_id' => (string) Str::uuid(),
            'date' => '2024-07-02',
            'slip_outline' => 'client66',
            'slip_memo' => 'outline67',
            'slip_entry_id' => $slipEntryId_2,
            'debit' => $accountId_1,
            'credit' => $accountId_3,
            'amount' => $amount_2,
            'client' => 'client72',
            'outline' => 'outline78',
            'credit_card_statement_id' => $creditCardStatementId,
        ];
        $slipEntriesOfStatement = [
            'statement' => [
                'slip_entries' => [$slipEntry_1],
                'total_amount' => $amount_1,
            ],
            'payment' => [
                'slip_entries' => [$slipEntry_2],
                'total_amount' => $amount_2,
            ],
        ];
        $creditCardStatements = [
            $creditCardStatementId => [
                'credit_card_statement_id' => $creditCardStatementId,
                'credit_card_statement_outline' => $creditCardStatementOutlone,
                'credit_card_statement_memo' => $creditCardStatementMemo,
                'date' => $creditCardStatementDate,
            ],
        ];
        $result_expected = [BookKeepingService::STATUS_NORMAL, [
            'credit_card_statement_id' => $creditCardStatementId,
            'credit_card_statement_outline' => $creditCardStatementOutlone,
            'credit_card_statement_memo' => $creditCardStatementMemo,
            'date' => $creditCardStatementDate,
            'statement' => [
                'slip_entries' => [$slipEntry_1],
                'total_amount' => $amount_1,
            ],
            'payment' => [
                'slip_entries' => [$slipEntry_2],
                'total_amount' => $amount_2,
            ],
            'unpaid_amount' => $amount_1 - $amount_2,
        ]];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckReadable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, $bookId]);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId, true)
            ->andReturn($accounts);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveSlipEntriesOfCreditCardStatement')
            ->once()
            ->with(
                $bookId,
                BookKeepingService::ORIGIN_DATE,
                (new Carbon())->format('Y-m-d'),
                $creditCardStatementId,
                [$accountId_1]
            )
            ->andReturn($slipEntriesOfStatement);
        /** @var \App\Service\CreditCardStatementService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementService::class);
        $creditCardStatementMock->shouldReceive('retrieveCreditCardStatements')
            ->once()
            ->with($bookId, $creditCardStatementId)
            ->andReturn($creditCardStatements);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock, $creditCardStatementMock);
        $result_actual = $BookKeeping->retrieveCreditCardStatement($creditCardStatementId, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_specified_book_is_not_readable(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 74;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $creditCardStatementId = (string) Str::uuid();
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckReadable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, '']);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldNotReceive('retrieveAccounts');
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldNotReceive('retrieveSlipEntriesOfCreditCardStatement');
        /** @var \App\Service\CreditCardStatementService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementService::class);
        $creditCardStatementMock->shouldNotReceive('retrieveCreditCardStatements');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock, $creditCardStatementMock);
        $result_actual = $BookKeeping->retrieveCreditCardStatement($creditCardStatementId, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }
    
    public function test_it_retrieves_nothing_because_the_credit_card_statement_for_the_specified_book_is_not_found(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 74;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $creditCardStatementId = (string) Str::uuid();
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckReadable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, $bookId]);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldNotReceive('retrieveAccounts');
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldNotReceive('retrieveSlipEntriesOfCreditCardStatement');
        /** @var \App\Service\CreditCardStatementService|\Mockery\MockInterface $creditCardStatementMock */
        $creditCardStatementMock = Mockery::mock(CreditCardStatementService::class);
        $creditCardStatementMock->shouldReceive('retrieveCreditCardStatements')
            ->once()
            ->with($bookId, $creditCardStatementId)
            ->andReturn([]);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock, $creditCardStatementMock);
        $result_actual = $BookKeeping->retrieveCreditCardStatement($creditCardStatementId, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }
}
