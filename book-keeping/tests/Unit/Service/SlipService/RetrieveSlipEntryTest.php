<?php

namespace Tests\Unit\Service\SlipService;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\DataProvider\SlipRepositoryInterface;
use App\Service\SlipService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveSlipEntryTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_retrieve_the_slip_entry(): void
    {
        $bookId = (string) Str::uuid();
        $slipId = (string) Str::uuid();
        $slipEntryId = (string) Str::uuid();
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $slip_expected = [
            'slip_id' => $slipId,
            'date' => '2020-03-31',
            'slip_outline' => 'slip_outline336',
            'slip_memo' => '',
            'slip_entry_id' => $slipEntryId,
            'debit' => $accountId1,
            'credit' => $accountId2,
            'amount' => 341,
            'client' => 'client342',
            'outline' => 'outline343',
        ];
        $draftInclude = true;
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('findById')
            ->once()
            ->with($slipEntryId, $bookId, $draftInclude)
            ->andReturn($slip_expected);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slip_actual = $slip->retrieveSlipEntry($slipEntryId, $bookId, $draftInclude);

        $this->assertSame($slip_expected, $slip_actual);
    }
}
