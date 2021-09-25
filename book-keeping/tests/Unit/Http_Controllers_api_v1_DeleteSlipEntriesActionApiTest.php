<?php

namespace Tests\Unit;

use App\Http\Controllers\api\v1\DeleteSlipEntriesActionApi;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Controllers_api_v1_DeleteSlipEntriesActionApiTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
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
        $BookKeepingMock->shouldNotReceive('deleteSlipEntryAsDraft');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new DeleteSlipEntriesActionApi($BookKeepingMock);
        $response_actual = $controller->__invoke($requestMock, $slipEntryId);

        $this->assertSame(JsonResponse::HTTP_NOT_FOUND, $response_actual->getStatusCode());
    }

    /**
     * @test
     */
    public function __invoke_ReturnErrorResponseBecauseUuidForTargetIsInvalid()
    {
        $slipEntryId = 'slipEntryId52';
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('validateUuid')
            ->once()
            ->with($slipEntryId)
            ->andReturn(false);
        $BookKeepingMock->shouldNotReceive('retrieveSlipEntry');
        $BookKeepingMock->shouldNotReceive('deleteSlipEntryAsDraft');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new DeleteSlipEntriesActionApi($BookKeepingMock);
        $response_actual = $controller->__invoke($requestMock, $slipEntryId);

        $this->assertSame(JsonResponse::HTTP_BAD_REQUEST, $response_actual->getStatusCode());
    }

    /**
     * @test
     */
    public function __invoke_ReturnNormalResponseForDelete()
    {
        $slipId = (string) Str::uuid();
        $slipEntryId = (string) Str::uuid();
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $slips = [
            $slipId => [
                'date'         => '2020-08-01',
                'slip_outline' => 'outline82',
                'slip_memo'    => 'memo83',
                'items'        => [
                    $slipEntryId => [
                        'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount'  => 880,
                        'client'  => 'client89',
                        'outline' => 'outline90',
                    ],
                ],
            ],
        ];
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('validateUuid')
            ->once()
            ->with($slipEntryId)
            ->andReturn(true);
        $BookKeepingMock->shouldReceive('retrieveSlipEntry')
            ->once()
            ->with($slipEntryId)
            ->andReturn($slips);
        $BookKeepingMock->shouldReceive('deleteSlipEntryAsDraft')
            ->once()
            ->with($slipEntryId);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new DeleteSlipEntriesActionApi($BookKeepingMock);
        $response_actual = $controller->__invoke($requestMock, $slipEntryId);

        $this->assertSame(JsonResponse::HTTP_NO_CONTENT, $response_actual->getStatusCode());
    }
}
