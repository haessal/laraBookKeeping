<?php

namespace Tests\Unit\Service\BookKeepingService;

use App\Service\AccountService;
use App\Service\BookKeepingService;
use App\Service\BookService;
use App\Service\BudgetService;
use App\Service\SlipService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class CreateSlipEntryAsDraftTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        Carbon::setTestNow();
    }

    public function test_it_creates_a_new_slip_entry_for_the_default_book_and_also_creates_a_new_slip_that_the_entry_is_bound_to(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 29;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 3600;
        $client = 'client37';
        $outline = 'outline38';
        $result_expected = [BookKeepingService::STATUS_NORMAL, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
            ->once()
            ->with(null, $userId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, $bookId]);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveDraftSlips')
            ->once()
            ->with($bookId)
            ->andReturn([]);
        $slipMock->shouldReceive('createSlipAsDraft')
            ->once()
            ->with($bookId, $outline, '2019-12-02', [
                ['debit' => $debit, 'client' => $client, 'outline' => $outline, 'credit' => $credit, 'amount' => $amount],
            ]);
        $slipMock->shouldNotReceive('createSlipEntry');
        Carbon::setTestNow(new Carbon('2019-12-02 09:59:59'));

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->createSlipEntryAsDraft($debit, $client, $outline, $credit, $amount);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_creates_a_new_slip_entry_for_the_specified_book_and_bind_the_entry_to_the_existing_slip(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 70;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slipId = (string) Str::uuid();
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 7900;
        $client = 'client80';
        $outline = 'outline81';
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
        $slipMock->shouldReceive('retrieveDraftSlips')
            ->once()
            ->with($bookId)
            ->andReturn([['slip_id' => $slipId]]);
        $slipMock->shouldNotReceive('createSlipAsDraft');
        $slipMock->shouldReceive('createSlipEntry')
            ->once()
            ->with($slipId, $debit, $credit, $amount, $client, $outline);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->createSlipEntryAsDraft($debit, $client, $outline, $credit, $amount, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }

    public function test_it_does_nothing_because_the_specified_book_is_not_readable(): void
    {
        $bookId = (string) Str::uuid();
        $userId = 111;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 1170;
        $client = 'client118';
        $outline = 'outline119';
        $result_expected = [BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, null];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBookOrCheckWritable')
            ->once()
            ->with($bookId, $userId)
            ->andReturn([BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE, '']);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldNotReceive('retrieveDraftSlips');
        $slipMock->shouldNotReceive('createSlipAsDraft');
        $slipMock->shouldNotReceive('createSlipEntry');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $result_actual = $BookKeeping->createSlipEntryAsDraft($debit, $client, $outline, $credit, $amount, $bookId);

        $this->assertSame($result_expected, $result_actual);
    }
}
