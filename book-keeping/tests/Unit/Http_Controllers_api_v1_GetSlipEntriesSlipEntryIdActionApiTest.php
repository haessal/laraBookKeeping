<?php

namespace Tests\Unit;

use App\Http\Controllers\api\v1\GetSlipEntriesSlipEntryIdActionApi;
use App\Http\Responder\api\v1\SlipEntriesJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Controllers_api_v1_GetSlipEntriesSlipEntryIdActionApiTest extends TestCase
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
                'date'         => '2020-03-03',
                'slip_outline' => 'outline43',
                'slip_memo'    => 'memo53',
                'items'        => [
                    $slipEntryId => [
                        'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount'  => 4400,
                        'client'  => 'client14',
                        'outline' => 'outline24',
                    ],
                ],
            ],
        ];
        $response_expected = new JsonResponse();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('validateUuid')
            ->once()
            ->with($slipEntryId)
            ->andReturn(true);
        $BookKeepingMock->shouldReceive('retrieveSlipEntry')
            ->once()
            ->with($slipEntryId)
            ->andReturn($context['slips']);
        /** @var \App\Http\Responder\api\v1\SlipEntriesJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipEntriesJsonResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new GetSlipEntriesSlipEntryIdActionApi($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $slipEntryId);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_ReturnErrorResponseBecauseTargetIsNotFound()
    {
        $slipEntryId = (string) Str::uuid();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('validateUuid')
            ->once()
            ->with($slipEntryId)
            ->andReturn(true);
        $BookKeepingMock->shouldReceive('retrieveSlipEntry')
            ->once()
            ->with($slipEntryId)
            ->andReturn([]);
        /** @var \App\Http\Responder\api\v1\SlipEntriesJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipEntriesJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new GetSlipEntriesSlipEntryIdActionApi($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $slipEntryId);

        $this->assertSame(JsonResponse::HTTP_NOT_FOUND, $response_actual->getStatusCode());
    }

    /**
     * @test
     */
    public function __invoke_ReturnErrorResponseBecauseUuidForTargetIsInvalid()
    {
        $slipEntryId = 'slipEntryId106';
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('validateUuid')
            ->once()
            ->with($slipEntryId)
            ->andReturn(false);
        $BookKeepingMock->shouldNotReceive('retrieveSlipEntry');
        /** @var \App\Http\Responder\api\v1\SlipEntriesJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipEntriesJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new GetSlipEntriesSlipEntryIdActionApi($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $slipEntryId);

        $this->assertSame(JsonResponse::HTTP_BAD_REQUEST, $response_actual->getStatusCode());
    }
}
