<?php

namespace Tests\Unit;

use App\Http\Controllers\api\v1\GetSlipEntriesActionApi;
use App\Http\Responder\api\v1\SlipEntriesJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mockery;
use ReflectionClass;
use Tests\TestCase;

class Http_Controllers_api_v1_GetSlipEntriesActionApiTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseForRequestedDate()
    {
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $query = [
            'from'    => ' 2020-01-01 ',
            'to'      => ' 2020-01-31 ',
            'debit'   => $accountId_1,
            'credit'  => $accountId_2,
            'operand' => ' and ',
            'keyword' => ' keyword ',
        ];
        $context['slips'] = [
            $slipId_1 => [
                'date'         => '2020-01-01',
                'slip_outline' => 'outline39',
                'slip_memo'    => 'memo41',
                'items'        => [
                    $slipEntryId_1 => [
                        'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount'  => 380,
                        'client'  => 'client46',
                        'outline' => 'outline47',
                    ],
                ],
            ],
        ];
        $response_expected = new JsonResponse();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('validatePeriod')
            ->once()
            ->with(trim($query['from']), trim($query['to']))
            ->andReturn(true);
        $BookKeepingMock->shouldReceive('validateUuid')
            ->once()
            ->with($accountId_1)
            ->andReturn(true);
        $BookKeepingMock->shouldReceive('validateUuid')
            ->once()
            ->with($accountId_2)
            ->andReturn(true);
        $BookKeepingMock->shouldReceive('retrieveSlips')
            ->once()
            ->with(trim($query['from']), trim($query['to']), trim($query['debit']), trim($query['credit']), trim($query['operand']), trim($query['keyword']))
            ->andReturn($context['slips']);
        /** @var \App\Http\Responder\api\v1\SlipEntriesJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipEntriesJsonResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn($query);

        $controller = new GetSlipEntriesActionApi($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseWithNoDateRequest()
    {
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('validatePeriod')
            ->once()
            ->with(null, null)
            ->andReturn(true);
        /** @var \App\Http\Responder\api\v1\SlipEntriesJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipEntriesJsonResponder::class);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn([]);

        $controller = new GetSlipEntriesActionApi($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame(JsonResponse::HTTP_BAD_REQUEST, $response_actual->getStatusCode());
    }

    /**
     * @test
     * @dataProvider forValidateAndTrimSlipEntriesQuery
     */
    public function validateAndTrimSlipEntriesQuery_MachValidationResult($query, $validateUuid_debit, $validateUuid_credit, $validatePeriod_from, $validatePeriod_to, $validatePeriod_return, $success_expected)
    {
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('validatePeriod')
            ->once()
            ->with($validatePeriod_from, $validatePeriod_to)
            ->andReturn($validatePeriod_return);
        if (array_key_exists('debit', $query)) {
            $BookKeepingMock->shouldReceive('validateUuid')
                ->once()
                ->with($query['debit'])
                ->andReturn($validateUuid_debit);
        }
        if (array_key_exists('credit', $query)) {
            $BookKeepingMock->shouldReceive('validateUuid')
                ->once()
                ->with($query['credit'])
                ->andReturn($validateUuid_credit);
        }
        /** @var \App\Http\Responder\api\v1\SlipEntriesJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipEntriesJsonResponder::class);

        $controller = new GetSlipEntriesActionApi($BookKeepingMock, $responderMock);
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('validateAndTrimSlipEntriesQuery');
        $method->setAccessible(true);
        $result_actual = $method->invoke($controller, $query);

        $this->assertSame($success_expected, $result_actual['success']);
    }

    public function forValidateAndTrimSlipEntriesQuery()
    {
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();

        return [
            [
                [
                    'from'    => ' 2020-01-01 ',
                    'to'      => ' 2020-01-31 ',
                    'debit'   => $accountId_1,
                    'credit'  => $accountId_2,
                    'operand' => ' and ',
                    'keyword' => ' keyword ',
                ],
                true,
                true,
                '2020-01-01',
                '2020-01-31',
                true,
                true,
            ],
            [
                [
                    'other' => 'other',
                ],
                null,
                null,
                null,
                null,
                true,
                false,
            ],
            [
                [],
                null,
                null,
                null,
                null,
                true,
                false,
            ],
            [
                [
                    'from' => ' 2020-01-40 ',
                ],
                null,
                null,
                '2020-01-40',
                null,
                false,
                false,
            ],
            [
                [
                    'operand' => 'xor',
                ],
                null,
                null,
                null,
                null,
                true,
                false,
            ],
            [
                [
                    'debit' => 'debit',
                ],
                false,
                null,
                null,
                null,
                true,
                false,
            ],
            [
                [
                    'credit' => 'credit',
                ],
                null,
                false,
                null,
                null,
                true,
                false,
            ],
            [
                [
                    'debit'  => $accountId_1,
                    'credit' => $accountId_2,
                ],
                true,
                true,
                null,
                null,
                true,
                false,
            ],
        ];
    }
}
