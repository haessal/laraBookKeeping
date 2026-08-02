<?php

namespace Tests\Unit\Service\SlipService;

use App\Repositories\SlipEntryRepositoryInterface;
use App\Repositories\SlipRepositoryInterface;
use App\Service\SlipService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveSlipEntriesOfCreditCardStatementTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_calls_repository_to_retrieve_a_list_of_slip_entries_as_history_and_payment_of_the_credit_card_statement(): void
    {
        $bookId = (string) Str::uuid();
        $fromDate = '2024-06-01';
        $toDate = '2024-06-30';
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accountId_3 = (string) Str::uuid();
        $accountId_4 = (string) Str::uuid();
        $creditCardAccountIds = [$accountId_1];
        $slipId_1 = (string) Str::uuid();
        $slipId_2 = (string) Str::uuid();
        $slipId_3 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntryId_2 = (string) Str::uuid();
        $slipEntryId_3 = (string) Str::uuid();
        $slipEntryId_4 = (string) Str::uuid();
        $amount_1 = 1037;
        $amount_2 = 380;
        $amount_3 = 3900;
        $amount_4 = 40000;
        $creditCardStatementId = (string) Str::uuid();
        $slipEntry_1 = [
            'slip_id' => $slipId_1,
            'date' => '2024-06-01',
            'slip_outline' => 'client147',
            'slip_memo' => 'outline148',
            'slip_entry_id' => $slipEntryId_1,
            'debit' => $accountId_1,
            'credit' => $accountId_2,
            'amount' => $amount_1,
            'client' => 'client47',
            'outline' => 'outline48',
            'credit_card_statement_id' => $creditCardStatementId,
        ];
        $slipEntry_2 = [
            'slip_id' => $slipId_1,
            'date' => '2024-06-01',
            'slip_outline' => 'client147',
            'slip_memo' => 'outline148',
            'slip_entry_id' => $slipEntryId_2,
            'debit' => $accountId_1,
            'credit' => $accountId_3,
            'amount' => $amount_2,
            'client' => 'client57',
            'outline' => 'outline58',
            'credit_card_statement_id' => $creditCardStatementId,
        ];
        $slipEntry_3 = [
            'slip_id' => $slipId_2,
            'date' => '2024-06-12',
            'slip_outline' => 'client267',
            'slip_memo' => 'outline268',
            'slip_entry_id' => $slipEntryId_3,
            'debit' => $accountId_1,
            'credit' => $accountId_3,
            'amount' => $amount_3,
            'client' => 'client67',
            'outline' => 'outline68',
            'credit_card_statement_id' => $creditCardStatementId,
        ];
        $slipEntry_4 = [
            'slip_id' => $slipId_3,
            'date' => '2024-06-23',
            'slip_outline' => 'client377',
            'slip_memo' => 'outline378',
            'slip_entry_id' => $slipEntryId_4,
            'debit' => $accountId_4,
            'credit' => $accountId_1,
            'amount' => $amount_4,
            'client' => 'client77',
            'outline' => 'outline78',
            'credit_card_statement_id' => $creditCardStatementId,
        ];
        $slipEntries = [
            $slipEntry_1, $slipEntry_2, $slipEntry_3, $slipEntry_4,
        ];
        $slipEntries_expected = [
            'statement' => [
                'slip_entries' => [$slipEntry_4],
                'total_amount' => $amount_4,
            ],
            'payment' => [
                'slip_entries' => [$slipEntry_1, $slipEntry_2, $slipEntry_3],
                'total_amount' => $amount_1 + $amount_2 + $amount_3,
            ],
        ];
        /** @var \App\Repositories\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        /** @var \App\Repositories\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('searchBook')
            ->once()
            ->with($bookId, $fromDate, $toDate, [
                'credit_card_account_ids' => $creditCardAccountIds,
                'credit_card_statement_id' => $creditCardStatementId,
            ])
            ->andReturn($slipEntries);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slipEntries_actual = $slip->retrieveSlipEntriesOfCreditCardStatement($bookId, $fromDate, $toDate, $creditCardStatementId, $creditCardAccountIds);

        $this->assertSame($slipEntries_expected, $slipEntries_actual);
    }
}
