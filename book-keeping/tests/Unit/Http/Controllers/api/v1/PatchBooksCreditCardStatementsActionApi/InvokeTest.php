<?php

namespace Tests\Unit\Http\Controllers\api\v1\PatchBooksCreditCardStatementsActionApi;

use App\Http\Controllers\api\v1\PatchBooksCreditCardStatementsActionApi;
use App\Http\Responder\api\v1\CreditCardStatementsJsonResponder;
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
        $bookId = (string) Str::uuid();
        $creditCardStatementId = (string) Str::uuid();
        $creditCardStatementContents = ['outline' => 'outline24'];
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($creditCardStatementId)
            ->andReturn(true);
        $serviceMock->shouldReceive('updateCreditCardStatement')
            ->once()
            ->with($creditCardStatementId, $creditCardStatementContents, $bookId)
            ->andReturn([-1, null]);
        /** @var \App\Http\Responder\api\v1\CreditCardStatementsJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreditCardStatementsJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')
            ->times(4)
            ->andReturn($creditCardStatementContents);

        $controller = new PatchBooksCreditCardStatementsActionApi($serviceMock, $responderMock);
        $response = $controller->__invoke($requestMock, $bookId, $creditCardStatementId);

        $this->assertSame(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}
