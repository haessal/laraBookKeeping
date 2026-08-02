<?php

namespace Tests\Unit\Service\SlipService;

use App\Repositories\SlipEntryRepositoryInterface;
use App\Repositories\SlipRepositoryInterface;
use App\Service\SlipService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveSlipTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_calls_repository_to_retrieve_the_slip(): void
    {
        $bookId = (string) Str::uuid();
        $slipId = (string) Str::uuid();
        $slip_expected = ['book_id' => $bookId, 'slip_id' => $slipId, 'date' => '2019-11-03', 'slip_outline' => 'outline', 'slip_memo' => 'memo'];
        /** @var \App\Repositories\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('findById')
            ->once()
            ->with($slipId, $bookId)
            ->andReturn($slip_expected);
        /** @var \App\Repositories\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slip_actual = $slip->retrieveSlip($slipId, $bookId);

        $this->assertSame($slip_expected, $slip_actual);
    }
}
