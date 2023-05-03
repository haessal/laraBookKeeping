<?php

namespace Tests\Unit\Service\SlipService;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\DataProvider\SlipRepositoryInterface;
use App\Service\SlipService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveAmountFlowsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_retrieve_the_amount_flow_of_each_account(): void
    {
        $fromDate = '2019-09-10';
        $toDate = '2019-09-11';
        $bookId = (string) Str::uuid();
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $amountFlows_expected = [
            $accountId1 => ['debit' => 210, 'credit' => 0],
            $accountId2 => ['debit' => 3000, 'credit' => 10],
            $accountId3 => ['debit' => 0, 'credit' => 3200],
        ];
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('searchBookAndCalculateSum')
            ->once()
            ->with($bookId, $fromDate, $toDate)
            ->andReturn($amountFlows_expected);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $amountFlows_actual = $slip->retrieveAmountFlows($fromDate, $toDate, $bookId);

        $this->assertSame($amountFlows_expected, $amountFlows_actual);
    }
}
