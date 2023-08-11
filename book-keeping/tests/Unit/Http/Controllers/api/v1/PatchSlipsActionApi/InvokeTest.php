<?php

namespace Tests\Unit\Http\Controllers\api\v1\PatchSlipsActionApi;

use App\Http\Controllers\api\v1\PatchSlipsActionApi;
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
        $slipId = (string) Str::uuid();
        $newOutline = 'outline24';
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($slipId)
            ->andReturn(true);
        $serviceMock->shouldReceive('updateSlip')
            ->once()
            ->with($slipId, ['outline' => $newOutline])
            ->andReturn([-1, null]);
        $serviceMock->shouldNotReceive('retrieveSlip');
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')
            ->times(4)
            ->andReturn([
                'outline' => $newOutline,
            ]);

        $controller = new PatchSlipsActionApi($serviceMock, $responderMock);
        $response = $controller->__invoke($requestMock, $slipId);

        $this->assertSame(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function test_it_returns_internal_server_error_without_slip_from_bookkeeping_service(): void
    {
        $slipId = (string) Str::uuid();
        $newMemo = 'memo56';
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($slipId)
            ->andReturn(true);
        $serviceMock->shouldReceive('updateSlip')
            ->once()
            ->with($slipId, ['memo' => $newMemo])
            ->andReturn([BookKeepingService::STATUS_NORMAL, null]);
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
            ->times(4)
            ->andReturn([
                'memo' => $newMemo,
            ]);

        $controller = new PatchSlipsActionApi($serviceMock, $responderMock);
        $response = $controller->__invoke($requestMock, $slipId);

        $this->assertSame(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}
