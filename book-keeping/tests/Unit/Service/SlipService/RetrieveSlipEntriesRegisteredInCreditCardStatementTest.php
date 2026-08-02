<?php

namespace Tests\Unit\Service\SlipService;

use App\Repositories\SlipEntryRepositoryInterface;
use App\Repositories\SlipRepositoryInterface;
use App\Service\SlipService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveSlipEntriesRegisteredInCreditCardStatementTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_calls_repository_to_retrieve_a_list_of_slip_entries_that_are_registered_in_the_credit_card_statement(): void
    {
        $bookId = (string) Str::uuid();
        $slipId = (string) Str::uuid();
        $slipEntryId = (string) Str::uuid();
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $creditCardStatementId = (string) Str::uuid();
        $slipEntries_expected = [
            [
                'slip_entry_id' => $slipEntryId,
                'slip_id' => $slipId,
                'debit' => $accountId1,
                'credit' => $accountId2,
                'amount' => 2240,
                'client' => 'client34',
                'outline' => 'outline35',
                'credit_card_statement_id' => $creditCardStatementId,
            ],
        ];
        /** @var \App\Repositories\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        /** @var \App\Repositories\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('searchBookWithCreditCardStatement')
            ->once()
            ->with($bookId, $creditCardStatementId)
            ->andReturn($slipEntries_expected);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slipEntries_actual = $slip->retrieveSlipEntriesRegisteredInCreditCardStatement($bookId, $creditCardStatementId);

        $this->assertSame($slipEntries_expected, $slipEntries_actual);
    }
}
