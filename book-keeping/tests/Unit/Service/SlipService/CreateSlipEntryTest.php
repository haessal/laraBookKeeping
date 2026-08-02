<?php

namespace Tests\Unit\Service\SlipService;

use App\Repositories\SlipEntryRepositoryInterface;
use App\Repositories\SlipRepositoryInterface;
use App\Service\SlipService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class CreateSlipEntryTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_calls_repository_to_create_a_new_slip_entry(): void
    {
        $slipId = (string) Str::uuid();
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 6400;
        $client = 'client4';
        $outline = 'outline4';
        $displayOrder = 1;
        $slipEntryId_expected = (string) Str::uuid();
        /** @var \App\Repositories\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        /** @var \App\Repositories\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('create')
            ->once()
            ->with($slipId, $debit, $credit, $amount, $client, $outline, $displayOrder)
            ->andReturn($slipEntryId_expected);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slipEntryId_actual = $slip->createSlipEntry($slipId, $debit, $credit, $amount, $client, $outline, $displayOrder);

        $this->assertSame($slipEntryId_expected, $slipEntryId_actual);
    }
}
