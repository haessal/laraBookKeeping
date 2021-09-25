<?php

namespace Tests\Unit;

use App\Http\Controllers\api\v1\PatchSlipsActionApi;
use App\Http\Responder\api\v1\SlipJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mockery;
use ReflectionClass;
use Tests\TestCase;

class Http_Controllers_api_v1_PatchSlipsActionApiTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseForRequestedData()
    {
        $slipId = (string) Str::uuid();
        $slipEntryId = (string) Str::uuid();
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $date = '2020-03-31';
        $date_updated = '2020-02-03';
        $slipContents = [
            'date'    => $date_updated,
            'outline' => 'outline33',
            'memo'    => 'memo34',
        ];
        $slips = [
            $slipId => [
                'date'         => $date,
                'slip_outline' => 'outline39',
                'slip_memo'    => 'memo40',
                'items'        => [
                    $slipEntryId => [
                        'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount'  => 450,
                        'client'  => 'client46',
                        'outline' => 'outline47',
                    ],
                ],
            ],
        ];
        $context['slips'] = [
            $slipId => [
                'date'         => $date_updated,
                'slip_outline' => 'outline33',
                'slip_memo'    => 'memo34',
                'items'        => [
                    $slipEntryId => [
                        'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount'  => 450,
                        'client'  => 'client46',
                        'outline' => 'outline47',
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
            ->twice()
            ->with($slipId)
            ->andReturn($slips, $context['slips']);
        $BookKeepingMock->shouldReceive('validateDateFormat')
            ->once()
            ->with($date_updated)
            ->andReturn(true);
        $BookKeepingMock->shouldReceive('updateSlip')
            ->once()
            ->with($slipId, $slipContents);
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn($slipContents);

        $controller = new PatchSlipsActionApi($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $slipId);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_ReturnErrorResponseBecauseRequestIsEmpty()
    {
        $slipId = (string) Str::uuid();
        $slipEntryId = (string) Str::uuid();
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $date = '2020-01-15';
        $slips = [
            $slipId => [
                'date'         => $date,
                'slip_outline' => 'outline119',
                'slip_memo'    => 'memo120',
                'items'        => [
                    $slipEntryId => [
                        'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount'  => 1250,
                        'client'  => 'client126',
                        'outline' => 'outline127',
                    ],
                ],
            ],
        ];
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('validateUuid')
            ->once()
            ->with($slipId)
            ->andReturn(true);
        $BookKeepingMock->shouldReceive('retrieveSlip')
            ->once()
            ->with($slipId)
            ->andReturn($slips);
        $BookKeepingMock->shouldNotReceive('updateSlip');
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn([]);

        $controller = new PatchSlipsActionApi($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $slipId);

        $this->assertSame(JsonResponse::HTTP_BAD_REQUEST, $response_actual->getStatusCode());
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
        $BookKeepingMock->shouldNotReceive('updateSlip');
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldNotReceive('all');

        $controller = new PatchSlipsActionApi($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $slipId);

        $this->assertSame(JsonResponse::HTTP_NOT_FOUND, $response_actual->getStatusCode());
    }

    /**
     * @test
     */
    public function __invoke_ReturnErrorResponseBecauseUuidForTargetIsInvalid()
    {
        $slipId = 'slip193';
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('validateUuid')
            ->once()
            ->with($slipId)
            ->andReturn(false);
        $BookKeepingMock->shouldNotReceive('retrieveSlip');
        $BookKeepingMock->shouldNotReceive('updateSlip');
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldNotReceive('all');

        $controller = new PatchSlipsActionApi($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $slipId);

        $this->assertSame(JsonResponse::HTTP_BAD_REQUEST, $response_actual->getStatusCode());
    }

    /**
     * @test
     * @dataProvider forValidateAndTrimSlipEntryContents
     */
    public function validateAndTrimSlipContents_MachValidationResult($slipContents, $string_expected, $validateDateFormatResult)
    {
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        if (array_key_exists('date', $slipContents)) {
            $BookKeepingMock->shouldReceive('validateDateFormat')
                ->once()
                ->with(trim($slipContents['date']))
                ->andReturn($validateDateFormatResult);
        }
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);

        $controller = new PatchSlipsActionApi($BookKeepingMock, $responderMock);
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('validateAndTrimSlipContents');
        $method->setAccessible(true);
        $string_actual = $method->invoke($controller, $slipContents);

        $this->assertSame($string_expected, $string_actual);
    }

    public function forValidateAndTrimSlipEntryContents()
    {
        return [
            [
                [
                    'date'    => '  2020-04-06  ',
                    'outline' => ' outline247 ',
                    'memo'    => ' memo248 ',
                ],
                [
                    'success'      => true,
                    'slipContents' => [
                        'date'    => '2020-04-06',
                        'outline' => 'outline247',
                        'memo'    => 'memo248',
                    ],
                ],
                true,
            ],
            [
                [
                    'date'    => '  2020-06-02  ',
                    'outline' => ' outline263 ',
                    'memo'    => ' memo264 ',
                    'other'   => 'other',
                ],
                ['success' => false, 'slipContents' => []],
                true,
            ],
            [
                [],
                ['success' => false, 'slipContents' => []],
                null,
            ],
            [
                [
                    'date' => '  2020-07-07  ',
                ],
                [
                    'success'      => true,
                    'slipContents' => [
                        'date' => '2020-07-07',
                    ],
                ],
                true,
            ],
            [
                [
                    'outline' => '  outline289  ',
                ],
                [
                    'success'      => true,
                    'slipContents' => [
                        'outline' => 'outline289',
                    ],
                ],
                null,
            ],
            [
                [
                    'memo' => '  memo301  ',
                ],
                [
                    'success'      => true,
                    'slipContents' => [
                        'memo' => 'memo301',
                    ],
                ],
                null,
            ],
            [
                [
                    'memo' => '    ',
                ],
                [
                    'success'      => true,
                    'slipContents' => [
                        'memo' => null,
                    ],
                ],
                null,
            ],
            [
                [
                    'date' => '',
                ],
                ['success' => false, 'slipContents' => []],
                false,
            ],
            [
                [
                    'date' => '  2020-03-32  ',
                ],
                ['success' => false, 'slipContents' => []],
                false,
            ],
            [
                [
                    'outline' => '',
                ],
                ['success' => false, 'slipContents' => []],
                null,
            ],
        ];
    }
}
