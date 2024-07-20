<?php

namespace Tests\Unit\Http\Controllers\api\v1\PostBooksCreditCardStatementsActionApi;

use App\Http\Controllers\api\v1\PostBooksCreditCardStatementsActionApi;
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
        $creditCardStatementOutline = 'outline24';
        $creditCardStatementMemo = 'memo25';
        $creditCardStatementDate = '2024-07-15';
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateDateFormat')
            ->once()
            ->with($creditCardStatementDate)
            ->andReturn(true);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('createCreditCardStatement')
            ->once()
            ->with(
                $creditCardStatementOutline,
                $creditCardStatementMemo,
                $creditCardStatementDate,
                null,
                $bookId,
            )
            ->andReturn([-1, null]);
        $serviceMock->shouldNotReceive('retrieveCreditCardStatements');
        /** @var \App\Http\Responder\api\v1\CreditCardStatementsJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreditCardStatementsJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn([
                'outline' => $creditCardStatementOutline,
                'memo' => $creditCardStatementMemo,
                'date' => $creditCardStatementDate,
            ]);

        $controller = new PostBooksCreditCardStatementsActionApi($serviceMock, $responderMock);
        $response = $controller->__invoke($requestMock, $bookId);

        $this->assertSame(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}
