<?php

namespace Tests\Unit\Service\SlipService;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\DataProvider\SlipRepositoryInterface;
use App\Service\SlipService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class RetrieveDraftSlipsTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_calls_repository_to_retrieve_a_list_of_draft_slips(): void
    {
        $bookId = (string) Str::uuid();
        $slipId = (string) Str::uuid();
        $slips_expected = [
            ['slip_id' => $slipId, 'date' => '2019-10-03', 'slip_outline' => 'outline', 'slip_memo' => 'memo'],
        ];
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('searchBookForDraft')
            ->once()
            ->with($bookId)
            ->andReturn($slips_expected);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slips_actual = $slip->retrieveDraftSlips($bookId);

        $this->assertSame($slips_expected, $slips_actual);
    }
}
