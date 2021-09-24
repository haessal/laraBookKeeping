<?php

namespace Tests\Unit;

use App\Http\Controllers\api\v1\PatchSlipEntriesActionApi;
use App\Http\Responder\api\v1\SlipEntriesJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mockery;
use ReflectionClass;
use Tests\TestCase;

class Http_Controllers_api_v1_PatchSlipEntriesActionApiTest extends TestCase
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
        $accountId_3 = (string) Str::uuid();
        $accountId_4 = (string) Str::uuid();
        $accounts = [
            $accountId_1 => ['account_title' => 'accountTitle_1'],
            $accountId_2 => ['account_title' => 'accountTitle_2'],
            $accountId_3 => ['account_title' => 'accountTitle_3'],
            $accountId_4 => ['account_title' => 'accountTitle_4'],
        ];
        $slipEntryContents = [
            'debit'   => $accountId_3,
            'credit'  => $accountId_4,
            'amount'  => 410,
            'client'  => 'client42',
            'outline' => 'outline43',
        ];
        $slips = [
            $slipId => [
                'date'         => '2020-03-02',
                'slip_outline' => 'outline48',
                'slip_memo'    => 'memo49',
                'items'        => [
                    $slipEntryId => [
                        'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount'  => 540,
                        'client'  => 'client55',
                        'outline' => 'outline56',
                    ],
                ],
            ],
        ];
        $context['slips'] = [
            $slipId => [
                'date'         => '2020-03-02',
                'slip_outline' => 'outline48',
                'slip_memo'    => 'memo49',
                'items'        => [
                    $slipEntryId => [
                        'debit'   => ['account_id' => $accountId_3, 'account_title' => 'accountTitle_3'],
                        'credit'  => ['account_id' => $accountId_4, 'account_title' => 'accountTitle_4'],
                        'amount'  => 410,
                        'client'  => 'client42',
                        'outline' => 'outline43',
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
            ->twice()
            ->with($slipEntryId)
            ->andReturn($slips, $context['slips']);
        $BookKeepingMock->shouldReceive('retrieveAccountsList')
            ->once()
            ->andReturn($accounts);
        $BookKeepingMock->shouldReceive('updateSlipEntry')
            ->once()
            ->with($slipEntryId, $slipEntryContents);
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
            ->andReturn($slipEntryContents);

        $controller = new PatchSlipEntriesActionApi($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $slipEntryId);

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
        $accounts = [
            $accountId_1 => ['account_title' => 'accountTitle_1'],
            $accountId_2 => ['account_title' => 'accountTitle_2'],
        ];
        $slips = [
            $slipId => [
                'date'         => '2020-03-02',
                'slip_outline' => 'outline48',
                'slip_memo'    => 'memo49',
                'items'        => [
                    $slipEntryId => [
                        'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount'  => 540,
                        'client'  => 'client55',
                        'outline' => 'outline56',
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
        $BookKeepingMock->shouldReceive('retrieveAccountsList')
            ->once()
            ->andReturn($accounts);
        $BookKeepingMock->shouldNotReceive('updateSlipEntry');
        /** @var \App\Http\Responder\api\v1\SlipEntriesJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipEntriesJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn([]);

        $controller = new PatchSlipEntriesActionApi($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $slipEntryId);

        $this->assertSame(JsonResponse::HTTP_BAD_REQUEST, $response_actual->getStatusCode());
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
        $BookKeepingMock->shouldNotReceive('retrieveAccountsList');
        $BookKeepingMock->shouldNotReceive('updateSlipEntry');
        /** @var \App\Http\Responder\api\v1\SlipEntriesJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipEntriesJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldNotReceive('all');

        $controller = new PatchSlipEntriesActionApi($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $slipEntryId);

        $this->assertSame(JsonResponse::HTTP_NOT_FOUND, $response_actual->getStatusCode());
    }

    /**
     * @test
     */
    public function __invoke_ReturnErrorResponseBecauseUuidForTargetIsInvalid()
    {
        $slipEntryId = 'slipEntryId206';
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('validateUuid')
            ->once()
            ->with($slipEntryId)
            ->andReturn(false);
        $BookKeepingMock->shouldNotReceive('retrieveSlipEntry');
        $BookKeepingMock->shouldNotReceive('retrieveAccountsList');
        $BookKeepingMock->shouldNotReceive('updateSlipEntry');
        /** @var \App\Http\Responder\api\v1\SlipEntriesJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipEntriesJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldNotReceive('all');

        $controller = new PatchSlipEntriesActionApi($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $slipEntryId);

        $this->assertSame(JsonResponse::HTTP_BAD_REQUEST, $response_actual->getStatusCode());
    }

    /**
     * @test
     * @dataProvider forValidateAndTrimSlipEntryContents
     */
    public function validateAndTrimSlipEntryContents_MachValidationResult($slipEntryContents, $accounts, $string_expected)
    {
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        /** @var \App\Http\Responder\api\v1\SlipEntriesJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipEntriesJsonResponder::class);

        $controller = new PatchSlipEntriesActionApi($BookKeepingMock, $responderMock);
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('validateAndTrimSlipEntryContents');
        $method->setAccessible(true);
        $string_actual = $method->invoke($controller, $slipEntryContents, $accounts);

        $this->assertSame($string_expected, $string_actual);
    }

    public function forValidateAndTrimSlipEntryContents()
    {
        return [
            [
                [
                    'debit'   => '  3274cc74-f7ab-40a4-984a-186a593401f7  ',
                    'credit'  => '   90dc7df5-07ea-4086-9461-0555c2a9d03c   ',
                    'amount'  => 257,
                    'client'  => ' client258 ',
                    'outline' => ' outline259 ',
                ],
                [
                    '3274cc74-f7ab-40a4-984a-186a593401f7' => ['account_title' => 'accountTitle_1'],
                    '90dc7df5-07ea-4086-9461-0555c2a9d03c' => ['account_title' => 'accountTitle_2'],
                ],
                [
                    'success'           => true,
                    'slipEntryContents' => [
                        'debit'   => '3274cc74-f7ab-40a4-984a-186a593401f7',
                        'credit'  => '90dc7df5-07ea-4086-9461-0555c2a9d03c',
                        'amount'  => 257,
                        'client'  => 'client258',
                        'outline' => 'outline259',
                    ],
                ],
            ],
            [
                [
                    'amount'  => 278,
                    'client'  => 'client279',
                    'outline' => 'outline280',
                    'other'   => 'other281',
                ],
                [],
                ['success' => false, 'slipEntryContents' => []],
            ],
            [
                [],
                [],
                ['success' => false, 'slipEntryContents' => []],
            ],
            [
                [
                    'debit'  => '  3274cc74-f7ab-40a4-984a-186a593401f7  ',
                    'credit' => '   90dc7df5-07ea-4086-9461-0555c2a9d03c   ',
                ],
                [
                    '3274cc74-f7ab-40a4-984a-186a593401f7' => ['account_title' => 'accountTitle_1'],
                    '90dc7df5-07ea-4086-9461-0555c2a9d03c' => ['account_title' => 'accountTitle_2'],
                ],
                [
                    'success'           => true,
                    'slipEntryContents' => [
                        'debit'  => '3274cc74-f7ab-40a4-984a-186a593401f7',
                        'credit' => '90dc7df5-07ea-4086-9461-0555c2a9d03c',
                    ],
                ],
            ],
            [
                [
                    'amount' => 310,
                ],
                [],
                [
                    'success'           => true,
                    'slipEntryContents' => [
                        'amount' => 310,
                    ],
                ],
            ],
            [
                [
                    'client' => ' client322 ',
                ],
                [],
                [
                    'success'           => true,
                    'slipEntryContents' => [
                        'client' => 'client322',
                    ],
                ],
            ],
            [
                [
                    'outline' => ' outline334 ',
                ],
                [],
                [
                    'success'           => true,
                    'slipEntryContents' => [
                        'outline' => 'outline334',
                    ],
                ],
            ],
            [
                [
                    'debit'  => '  3274cc74-f7ab-40a4-984a-186a593401f7  ',
                    'credit' => '   90dc7df5-07ea-4086-9461-0555c2a9d03c   ',
                ],
                [
                    '90dc7df5-07ea-4086-9461-0555c2a9d03c' => ['account_title' => 'accountTitle_2'],
                ],
                ['success' => false, 'slipEntryContents' => []],
            ],
            [
                [
                    'debit'  => '  3274cc74-f7ab-40a4-984a-186a593401f7  ',
                    'credit' => '   90dc7df5-07ea-4086-9461-0555c2a9d03c   ',
                ],
                [
                    '3274cc74-f7ab-40a4-984a-186a593401f7' => ['account_title' => 'accountTitle_1'],
                ],
                ['success' => false, 'slipEntryContents' => []],
            ],
            [
                [
                    'debit'  => '  3274cc74-f7ab-40a4-984a-186a593401f7  ',
                ],
                [
                    '3274cc74-f7ab-40a4-984a-186a593401f7' => ['account_title' => 'accountTitle_1'],
                    '90dc7df5-07ea-4086-9461-0555c2a9d03c' => ['account_title' => 'accountTitle_2'],
                ],
                ['success' => false, 'slipEntryContents' => []],
            ],
            [
                [
                    'credit' => '   90dc7df5-07ea-4086-9461-0555c2a9d03c   ',
                ],
                [
                    '3274cc74-f7ab-40a4-984a-186a593401f7' => ['account_title' => 'accountTitle_1'],
                    '90dc7df5-07ea-4086-9461-0555c2a9d03c' => ['account_title' => 'accountTitle_2'],
                ],
                ['success' => false, 'slipEntryContents' => []],
            ],
            [
                [
                    'debit'  => '  3274cc74-f7ab-40a4-984a-186a593401f7  ',
                    'credit' => '   3274cc74-f7ab-40a4-984a-186a593401f7   ',
                ],
                [
                    '3274cc74-f7ab-40a4-984a-186a593401f7' => ['account_title' => 'accountTitle_1'],
                    '90dc7df5-07ea-4086-9461-0555c2a9d03c' => ['account_title' => 'accountTitle_2'],
                ],
                ['success' => false, 'slipEntryContents' => []],
            ],
            [
                [
                    'amount' => 0,
                ],
                [],
                ['success' => false, 'slipEntryContents' => []],
            ],
            [
                [
                    'amount' => '404',
                ],
                [],
                ['success' => false, 'slipEntryContents' => []],
            ],
            [
                [
                    'client' => '',
                ],
                [],
                ['success' => false, 'slipEntryContents' => []],
            ],
            [
                [
                    'outline' => '',
                ],
                [],
                ['success' => false, 'slipEntryContents' => []],
            ],
        ];
    }
}
