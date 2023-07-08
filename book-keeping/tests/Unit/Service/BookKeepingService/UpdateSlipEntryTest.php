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

class UpdateSlipEntryTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_updates_the_slip_entry_for_the_specified_book(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 24;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slipEntryId = (string) Str::uuid();
        $slipEntry = [
            'slip_id'       => (string) Str::uuid(),
            'date'          => '2023-07-08',
            'slip_outline'  => 'slip_outline33',
            'slip_memo'     => 'slip_memo34',
            'slip_entry_id' => $slipEntryId,
            'debit'         => (string) Str::uuid(),
            'credit'        => (string) Str::uuid(),
            'amount'        => 3800,
            'client'        => 'client39',
            'outline'       => 'outline40',
        ];
        $newData = ['amount' => 10000];
        $result_expected = [BookKeepingService::STATUS_NORMAL, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, $bookId]);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn([]);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveSlipEntry')
            ->once()
            ->with($slipEntryId, $bookId, false)
            ->andReturn($slipEntry);
        $slipMock->shouldReceive('updateSlipEntry')
            ->once()
            ->with($slipEntryId, $newData);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->updateSlipEntry($slipEntryId, $newData, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_specified_book_is_not_readable(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 24;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slipEntryId = (string) Str::uuid();
        $newData = ['amount' => 10000];
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
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
        $slipMock->shouldNotReceive('retrieveSlipEntry');
        $slipMock->shouldNotReceive('updateSlipEntry');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->updateSlipEntry($slipEntryId, $newData, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_slip_entry_for_the_specified_book_is_not_found(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 109;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slipEntryId = (string) Str::uuid();
        $newData = ['amount' => 10000];
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
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
        $slipMock->shouldReceive('retrieveSlipEntry')
            ->once()
            ->with($slipEntryId, $bookId, false)
            ->andReturn(null);
        $slipMock->shouldNotReceive('updateSlipEntry');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->updateSlipEntry($slipEntryId, $newData, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_debit_in_new_data_is_invalid_account(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 109;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slipEntryId = (string) Str::uuid();
        $slipEntry = [
            'slip_id'       => (string) Str::uuid(),
            'date'          => '2023-07-08',
            'slip_outline'  => 'slip_outline152',
            'slip_memo'     => 'slip_memo153',
            'slip_entry_id' => $slipEntryId,
            'debit'         => (string) Str::uuid(),
            'credit'        => (string) Str::uuid(),
            'amount'        => 1570,
            'client'        => 'client158',
            'outline'       => 'outline159',
        ];
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accounts = [
            $accountId2 => [],
        ];
        $newData = ['debit' => $accountId1, 'credit' => $accountId2];
        $result_expected = [BookKeepingService::STATUS_ERROR_BAD_CONDITION, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, $bookId]);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn($accounts);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveSlipEntry')
            ->once()
            ->with($slipEntryId, $bookId, false)
            ->andReturn($slipEntry);
        $slipMock->shouldNotReceive('updateSlipEntry');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->updateSlipEntry($slipEntryId, $newData, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_credit_in_new_data_is_invalid_account(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 109;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slipEntryId = (string) Str::uuid();
        $slipEntry = [
            'slip_id'       => (string) Str::uuid(),
            'date'          => '2023-07-08',
            'slip_outline'  => 'slip_outline207',
            'slip_memo'     => 'slip_memo208',
            'slip_entry_id' => $slipEntryId,
            'debit'         => (string) Str::uuid(),
            'credit'        => (string) Str::uuid(),
            'amount'        => 2120,
            'client'        => 'client213',
            'outline'       => 'outline214',
        ];
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accounts = [
            $accountId1 => [],
        ];
        $newData = ['debit' => $accountId1, 'credit' => $accountId2];
        $result_expected = [BookKeepingService::STATUS_ERROR_BAD_CONDITION, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, $bookId]);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn($accounts);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveSlipEntry')
            ->once()
            ->with($slipEntryId, $bookId, false)
            ->andReturn($slipEntry);
        $slipMock->shouldNotReceive('updateSlipEntry');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->updateSlipEntry($slipEntryId, $newData, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }
}
