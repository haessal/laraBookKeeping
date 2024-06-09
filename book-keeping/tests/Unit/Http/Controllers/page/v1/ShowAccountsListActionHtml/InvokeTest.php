<?php

namespace Tests\Unit\Http\Controllers\page\v1\ShowAccountsListActionHtml;

use App\Http\Controllers\page\v1\ShowAccountsListActionHTML;
use App\Http\Responder\page\v1\ShowAccountsListViewResponder;
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
            ->with(false)
            ->andReturn([-1, null]);
        /** @var \App\Http\Responder\page\v1\ShowAccountsListViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(ShowAccountsListViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new ShowAccountsListActionHTML($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }
}
