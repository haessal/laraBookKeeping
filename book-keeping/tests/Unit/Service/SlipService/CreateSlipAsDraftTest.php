<?php

namespace Tests\Unit\Service\SlipService;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\DataProvider\SlipRepositoryInterface;
use App\Service\SlipService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class CreateSlipAsDraftTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_create_a_new_slip_as_draft(): void
    {
        $bookId = (string) Str::uuid();
        $outline = 'slip_outline1';
        $date = '2019-08-17';
        $memo = 'memo1';
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $accountId4 = (string) Str::uuid();
        $entries = [
            ['debit' => $accountId1, 'credit' => $accountId2, 'amount' => 1100, 'client' => 'client11', 'outline' => 'outline11'],
            ['debit' => $accountId3, 'credit' => $accountId4, 'amount' => 2200, 'client' => 'client22', 'outline' => 'outline22'],
        ];
        $displayOrder = 1;
        $slipId_expected = (string) Str::uuid();
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('create')
            ->once()
            ->with($bookId, $outline, $date, $memo, $displayOrder, true)
            ->andReturn($slipId_expected);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('create')
            ->once()
            ->with($slipId_expected, $entries[0]['debit'], $entries[0]['credit'], $entries[0]['amount'], $entries[0]['client'], $entries[0]['outline'], null);
        $slipEntryMock->shouldReceive('create')
            ->once()
            ->with($slipId_expected, $entries[1]['debit'], $entries[1]['credit'], $entries[1]['amount'], $entries[1]['client'], $entries[1]['outline'], null);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slipId_actual = $slip->createSlipAsDraft($bookId, $outline, $date, $entries, $memo, $displayOrder);

        $this->assertSame($slipId_expected, $slipId_actual);
    }

    public function test_it_calls_repository_to_create_a_new_slip_as_draft_with_display_order_of_the_entry(): void
    {
        $bookId = (string) Str::uuid();
        $outline = 'slip_outline1';
        $date = '2019-08-17';
        $memo = 'memo1';
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $accountId4 = (string) Str::uuid();
        $entries = [
            ['debit' => $accountId1, 'credit' => $accountId2, 'amount' => 1100, 'client' => 'client11', 'outline' => 'outline11', 'display_order' => 0],
            ['debit' => $accountId3, 'credit' => $accountId4, 'amount' => 2200, 'client' => 'client22', 'outline' => 'outline22', 'display_order' => 1],
        ];
        $displayOrder = 1;
        $slipId_expected = (string) Str::uuid();
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('create')
            ->once()
            ->with($bookId, $outline, $date, $memo, $displayOrder, true)
            ->andReturn($slipId_expected);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('create')
            ->once()
            ->with($slipId_expected, $entries[0]['debit'], $entries[0]['credit'], $entries[0]['amount'], $entries[0]['client'], $entries[0]['outline'], 0);
        $slipEntryMock->shouldReceive('create')
            ->once()
            ->with($slipId_expected, $entries[1]['debit'], $entries[1]['credit'], $entries[1]['amount'], $entries[1]['client'], $entries[1]['outline'], 1);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slipId_actual = $slip->createSlipAsDraft($bookId, $outline, $date, $entries, $memo, $displayOrder);

        $this->assertSame($slipId_expected, $slipId_actual);
    }
}
