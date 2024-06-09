<?php

namespace Tests\Unit\Http\Controllers\page\v1\FindSlipsActionHtml;

use App\Http\Controllers\page\v1\FindSlipsActionHTML;
use App\Http\Responder\page\v1\FindSlipsViewResponder;
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
        /** @var \App\Http\Responder\page\v1\FindSlipsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(FindSlipsViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldNotReceive('isMethod');
        $requestMock->shouldNotReceive('input');

        $controller = new FindSlipsActionHTML($serviceMock, $responderMock);
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
        /** @var \App\Http\Responder\page\v1\FindSlipsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(FindSlipsViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldNotReceive('isMethod');
        $requestMock->shouldNotReceive('input');

        $controller = new FindSlipsActionHTML($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }

    public function test_it_returns_view_with_unexpected_post_data(): void
    {
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('retrieveCategorizedAccounts')
            ->once()
            ->with(true)
            ->andReturn([BookKeepingService::STATUS_NORMAL, []]);
        /** @var \App\Http\Responder\page\v1\FindSlipsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(FindSlipsViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with([
                'accounts' => [],
                'beginning_date' => '',
                'end_date' => '',
                'debit' => '',
                'credit' => '',
                'and_or' => '',
                'keyword' => '',
                'slips' => [],
                'message' => __('There is no condition for search.'),
            ])
            ->andReturn(new Response(Response::HTTP_OK));
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('buttons')
            ->andReturn(null);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('modify_no_list')
            ->andReturn([]);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('BEGINNING')
            ->andReturn(null);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('END')
            ->andReturn(null);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('debit')
            ->andReturn(null);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('credit')
            ->andReturn(null);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('and_or')
            ->andReturn(null);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('KEYWORD')
            ->andReturn(null);

        $controller = new FindSlipsActionHTML($serviceMock, $responderMock);
        $response = $controller->__invoke($requestMock);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }
}
