<?php

namespace Tests\Unit\Http\Controllers\api\v1\GetSlipEntriesSlipEntryIdActionApi;

use App\Http\Controllers\api\v1\GetSlipEntriesSlipEntryIdActionApi;
use App\Http\Responder\api\v1\SlipEntriesJsonResponder;
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
        $slipEntryId = (string) Str::uuid();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($slipEntryId)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveSlipEntry')
            ->once()
            ->with($slipEntryId)
            ->andReturn([-1, null]);
        /** @var \App\Http\Responder\api\v1\SlipEntriesJsonResponder |\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipEntriesJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new GetSlipEntriesSlipEntryIdActionApi($serviceMock, $responderMock);
        $response = $controller->__invoke($requestMock, $slipEntryId);

        $this->assertSame(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function test_it_returns_internal_server_error_without_slip_entry_from_bookkeeping_service(): void
    {
        $slipEntryId = (string) Str::uuid();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($slipEntryId)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveSlipEntry')
            ->once()
            ->with($slipEntryId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, null]);
        /** @var \App\Http\Responder\api\v1\SlipEntriesJsonResponder |\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipEntriesJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new GetSlipEntriesSlipEntryIdActionApi($serviceMock, $responderMock);
        $response = $controller->__invoke($requestMock, $slipEntryId);

        $this->assertSame(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}