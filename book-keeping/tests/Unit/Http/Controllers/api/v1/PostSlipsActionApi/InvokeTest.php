<?php

namespace Tests\Unit\Http\Controllers\api\v1\PostSlipsActionApi;

use App\Http\Controllers\api\v1\PostSlipsActionApi;
use App\Http\Responder\api\v1\SlipJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class InvokeTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior(): void
    {
        $slipDate = '2023-07-16';
        $slipOutline = 'outline24';
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $slipEntries[] =
            [
                'debit' => $debit,
                'credit' => $credit,
                'amount' => 31,
                'client' => 'client32',
                'outline' => 'outline33',
                'display_order' => 0,
            ];
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateDateFormat')
            ->once()
            ->with($slipDate)
            ->andReturn(true);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($debit)
            ->andReturn(true);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($credit)
            ->andReturn(true);
        $serviceMock->shouldReceive('createSlip')
            ->once()
            ->with($slipOutline, $slipDate, $slipEntries, null)
            ->andReturn([-1, null]);
        $serviceMock->shouldNotReceive('retrieveSlip');
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn([
                'date' => $slipDate,
                'outline' => $slipOutline,
                'memo' => null,
                'entries' => $slipEntries,
            ]);

        $controller = new PostSlipsActionApi($serviceMock, $responderMock);
        $response = $controller->__invoke($requestMock);

        $this->assertSame(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function test_it_returns_internal_server_error_without_slip_from_bookkeeping_service(): void
    {
        $slipDate = '2023-07-17';
        $slipOutline = 'outline78';
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $slipEntries[] =
            [
                'debit' => $debit,
                'credit' => $credit,
                'amount' => 85,
                'client' => 'client86',
                'outline' => 'outline87',
                'display_order' => 0,
            ];
        $slipId = (string) Str::uuid();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateDateFormat')
            ->once()
            ->with($slipDate)
            ->andReturn(true);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($debit)
            ->andReturn(true);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($credit)
            ->andReturn(true);
        $serviceMock->shouldReceive('createSlip')
            ->once()
            ->with($slipOutline, $slipDate, $slipEntries, null)
            ->andReturn([BookKeepingService::STATUS_NORMAL, $slipId]);
        $serviceMock->shouldReceive('retrieveSlip')
            ->once()
            ->with($slipId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, null]);
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn([
                'date' => $slipDate,
                'outline' => $slipOutline,
                'memo' => null,
                'entries' => $slipEntries,
            ]);

        $controller = new PostSlipsActionApi($serviceMock, $responderMock);
        $response = $controller->__invoke($requestMock);

        $this->assertSame(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}
