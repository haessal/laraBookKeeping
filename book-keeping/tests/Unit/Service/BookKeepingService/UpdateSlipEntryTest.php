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

class UpdateSlipEntryTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_updates_the_slip_entry_for_the_specified_book(): void
    {
        $bookId = (string) Str::uuid();
        $slipEntryId = (string) Str::uuid();
        $newData = ['amount' => 10000];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('updateSlipEntry')
            ->once()
            ->with($slipEntryId, $newData);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $BookKeeping->updateSlipEntry($slipEntryId, $newData, $bookId);

        $this->assertTrue(true);
    }
}
