<?php

namespace Tests\Unit\Service\SlipService;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\DataProvider\SlipRepositoryInterface;
use App\Service\SlipService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveSlipEntriesBoundToTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_retrieve_a_list_of_slip_entries_that_are_bound_to_the_slip(): void
    {
        $slipId = (string) Str::uuid();
        $slipEntryId = (string) Str::uuid();
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $slipEntries_expected = [
            [
                'slip_entry_id' => $slipEntryId,
                'slip_id'       => $slipId,
                'debit'         => $accountId1,
                'credit'        => $accountId2,
                'amount'        => 2240,
                'client'        => 'client5',
                'outline'       => 'outline5',
            ],
        ];
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('searchSlip')
            ->once()
            ->with($slipId)
            ->andReturn($slipEntries_expected);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slipEntries_actual = $slip->retrieveSlipEntriesBoundTo($slipId);

        $this->assertSame($slipEntries_expected, $slipEntries_actual);
    }
}
