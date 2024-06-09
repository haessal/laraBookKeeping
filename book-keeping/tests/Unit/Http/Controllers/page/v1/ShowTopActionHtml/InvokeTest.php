<?php

namespace Tests\Unit\Http\Controllers\page\v1\ShowTopActionHtml;

use App\Http\Controllers\page\v1\ShowTopActionHTML;
use App\Http\Responder\page\v1\ShowTopViewResponder;
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

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior(): void
    {
        $today = date('Y-m-d');
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('retrieveProfitLossBalanceSheetSlipsOfOneDay')
            ->once()
            ->with($today)
            ->andReturn([-1, null]);
        /** @var \App\Http\Responder\page\v1\ShowTopViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(ShowTopViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new ShowTopActionHTML($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }
}
