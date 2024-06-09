<?php

namespace Tests\Unit\Http\Controllers\page\v1\ShowStatementsActionHtml;

use App\Http\Controllers\page\v1\ShowStatementsActionHTML;
use App\Http\Responder\page\v1\ShowStatementsViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class InvokeTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior_1(): void
    {
        $today = date('Y-m-d');
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validatePeriod')
            ->once()
            ->with($today, $today)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveProfitLossTrialBalanceBalanceSheetsSlips')
            ->once()
            ->with($today, $today)
            ->andReturn([BookKeepingService::STATUS_NORMAL, null]);
        $serviceMock->shouldNotReceive('retrieveDraftSlips');
        /** @var \App\Http\Responder\page\v1\ShowStatementsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(ShowStatementsViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(false);
        $requestMock->shouldNotReceive('input');

        $controller = new ShowStatementsActionHTML($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior_2(): void
    {
        $today = date('Y-m-d');
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validatePeriod')
            ->once()
            ->with($today, $today)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveProfitLossTrialBalanceBalanceSheetsSlips')
            ->once()
            ->with($today, $today)
            ->andReturn([-1, null]);
        $serviceMock->shouldNotReceive('retrieveDraftSlips');
        /** @var \App\Http\Responder\page\v1\ShowStatementsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(ShowStatementsViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(false);
        $requestMock->shouldNotReceive('input');

        $controller = new ShowStatementsActionHTML($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }
}
