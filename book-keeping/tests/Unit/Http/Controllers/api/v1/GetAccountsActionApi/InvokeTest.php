<?php

namespace Tests\Unit\Http\Controllers\api\v1\GetAccountsActionApi;

use App\Http\Controllers\api\v1\GetAccountsActionApi;
use App\Http\Responder\api\v1\AccountsJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('retrieveAccounts')
            ->once()
            ->andReturn([-1, null]);
        /** @var \App\Http\Responder\api\v1\AccountsJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(AccountsJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new GetAccountsActionApi($serviceMock, $responderMock);
        $response = $controller->__invoke($requestMock);

        $this->assertSame(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function test_it_returns_internal_server_error_without_account_from_bookkeeping_service(): void
    {
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('retrieveAccounts')
            ->once()
            ->andReturn([BookKeepingService::STATUS_NORMAL, null]);
        /** @var \App\Http\Responder\api\v1\AccountsJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(AccountsJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new GetAccountsActionApi($serviceMock, $responderMock);
        $response = $controller->__invoke($requestMock);

        $this->assertSame(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}