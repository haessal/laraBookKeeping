<?php

namespace Tests\Unit;

use App\Http\Controllers\api\v1\GetSlipsSlipIdActionApi;
use App\Http\Responder\api\v1\SlipJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Controllers_api_v1_GetSlipsSlipIdActionApiTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponse()
    {
        $slipId = (string) Str::uuid();
        $slipEntryId = (string) Str::uuid();
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $context['slips'] = [
            $slipId => [
                'date'         => '2020-03-30',
                'slip_outline' => 'outline31',
                'slip_memo'    => 'memo32',
                'items'        => [
                    $slipEntryId => [
                        'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount'  => 4000,
                        'client'  => 'client41',
                        'outline' => 'outline42',
                    ],
                ],
            ],
        ];
        $response_expected = new JsonResponse();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('validateUuid')
            ->once()
            ->with($slipId)
            ->andReturn(true);
        $BookKeepingMock->shouldReceive('retrieveSlip')
            ->once()
            ->with($slipId)
            ->andReturn($context['slips']);
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new GetSlipsSlipIdActionApi($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $slipId);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_ReturnErrorResponseBecauseTargetIsNotFound()
    {
        $slipId = (string) Str::uuid();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('validateUuid')
            ->once()
            ->with($slipId)
            ->andReturn(true);
        $BookKeepingMock->shouldReceive('retrieveSlip')
            ->once()
            ->with($slipId)
            ->andReturn([]);
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new GetSlipsSlipIdActionApi($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $slipId);

        $this->assertSame(JsonResponse::HTTP_NOT_FOUND, $response_actual->getStatusCode());
    }

    /**
     * @test
     */
    public function __invoke_ReturnErrorResponseBecauseUuidForTargetIsInvalid()
    {
        $slipId = 'slipId106';
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('validateUuid')
            ->once()
            ->with($slipId)
            ->andReturn(false);
        $BookKeepingMock->shouldNotReceive('retrieveSlip');
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new GetSlipsSlipIdActionApi($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $slipId);

        $this->assertSame(JsonResponse::HTTP_BAD_REQUEST, $response_actual->getStatusCode());
    }
}
