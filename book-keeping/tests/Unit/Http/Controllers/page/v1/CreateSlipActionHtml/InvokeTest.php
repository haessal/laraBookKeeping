<?php

namespace Tests\Unit\Http\Controllers\page\v1\CreateSlipActionHtml;

use App\Http\Controllers\page\v1\CreateSlipActionHTML;
use App\Http\Responder\page\v1\CreateSlipViewResponder;
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
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('retrieveCategorizedAccounts')
            ->once()
            ->with(true)
            ->andReturn([BookKeepingService::STATUS_NORMAL, null]);
        $serviceMock->shouldNotReceive('retrieveDraftSlips');
        /** @var \App\Http\Responder\page\v1\CreateSlipViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateSlipViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldNotReceive('isMethod');
        $requestMock->shouldNotReceive('input');

        $controller = new CreateSlipActionHTML($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior_2(): void
    {
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('retrieveCategorizedAccounts')
            ->once()
            ->with(true)
            ->andReturn([-1, []]);
        $serviceMock->shouldNotReceive('retrieveDraftSlips');
        /** @var \App\Http\Responder\page\v1\CreateSlipViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateSlipViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldNotReceive('isMethod');
        $requestMock->shouldNotReceive('input');

        $controller = new CreateSlipActionHTML($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior_3(): void
    {
        $today = date('Y-m-d');
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('retrieveCategorizedAccounts')
            ->once()
            ->with(true)
            ->andReturn([BookKeepingService::STATUS_NORMAL, []]);
        $serviceMock->shouldReceive('retrieveDraftSlips')
            ->once()
            ->andReturn([-1, null]);
        /** @var \App\Http\Responder\page\v1\CreateSlipViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateSlipViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(false);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('date')
            ->andReturn($today);

        $controller = new CreateSlipActionHTML($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }

    public function test_it_returns_view_with_unexpected_post_data(): void
    {
        $today = date('Y-m-d');
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('retrieveCategorizedAccounts')
            ->once()
            ->with(true)
            ->andReturn([BookKeepingService::STATUS_NORMAL, []]);
        $serviceMock->shouldReceive('retrieveDraftSlips')
            ->once()
            ->andReturn([BookKeepingService::STATUS_NORMAL, []]);
        /** @var \App\Http\Responder\page\v1\CreateSlipViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateSlipViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with([
                'accounts' => [],
                'add' => null,
                'slipdate' => $today,
                'totalamount' => 0,
                'draftslip' => [],
            ])
            ->andReturn(new Response(Response::HTTP_OK));
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('date')
            ->andReturn($today);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('buttons')
            ->andReturn(null);

        $controller = new CreateSlipActionHTML($serviceMock, $responderMock);
        $response = $controller->__invoke($requestMock);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }
}
