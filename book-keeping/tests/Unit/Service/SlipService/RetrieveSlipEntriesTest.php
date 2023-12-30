<?php

namespace Tests\Unit\Service\SlipService;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\DataProvider\SlipRepositoryInterface;
use App\Service\SlipService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveSlipEntriesTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_retrieve_a_list_of_slip_entires(): void
    {
        $fromDate = '2019-08-10';
        $toDate = '2019-08-15';
        $bookId = (string) Str::uuid();
        $slipId = (string) Str::uuid();
        $slipEntryId = (string) Str::uuid();
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();

        $slipEntries_expected = [
            [
                'slip_id' => $slipId,
                'date' => '2019-08-11',
                'slip_outline' => 'slip_outline3',
                'slip_memo' => 'memo3',
                'slip_entry_id' => $slipEntryId,
                'debit' => $accountId1,
                'credit' => $accountId2,
                'amount' => 100,
                'client' => 'client3',
                'outline' => 'outline3',
            ],
        ];
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('searchBook')
            ->once()
            ->with($bookId, $fromDate, $toDate, [])
            ->andReturn($slipEntries_expected);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slipEntries_actual = $slip->retrieveSlipEntries($fromDate, $toDate, [], $bookId);

        $this->assertSame($slipEntries_expected, $slipEntries_actual);
    }
}
