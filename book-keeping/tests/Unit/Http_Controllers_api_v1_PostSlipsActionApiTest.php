<?php

namespace Tests\Unit;

use App\Http\Controllers\api\v1\PostSlipsActionApi;
use App\Http\Responder\api\v1\SlipJsonResponder;
use App\Service\BookKeepingService;
use Mockery;
use ReflectionClass;
use Tests\TestCase;

class Http_Controllers_api_v1_PostSlipsActionApiTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    /**
     * @test
     * @dataProvider forValidateAndTrimString
     */
    public function validateAndTrimString($array_in, $key, $string_expected)
    {
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);

        $controller = new PostSlipsActionApi($BookKeepingMock, $responderMock);
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('validateAndTrimString');
        $method->setAccessible(true);
        $string_actual = $method->invoke($controller, $array_in, $key);

        $this->assertSame($string_expected, $string_actual);
    }

    public function forValidateAndTrimString()
    {
        return [
            [[], 'outline', null,],
            [['outline' => ['  outline1']], 'outline', null,],
            [['outline' => '  '], 'outline', null,],
            [['outline' => '  outline1'], 'outline', 'outline1',],
        ];
    }

    /**
     * @test
     * @dataProvider forValidateAndTrimAccounts
     */
    public function validateAndTrimAccounts($array_in, $key, $accounts, $string_expected)
    {
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);

        $controller = new PostSlipsActionApi($BookKeepingMock, $responderMock);
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('validateAndTrimAccounts');
        $method->setAccessible(true);
        $string_actual = $method->invoke($controller, $array_in, $key, $accounts);

        $this->assertSame($string_expected, $string_actual);

    }

    public function forValidateAndTrimAccounts()
    {
        return [
            [
                ['debit' => 0],
                'debit',
                ['3274cc74-f7ab-40a4-984a-186a593401f7' => null,],
                null,
            ],
            [
                ['debit' => '  471b26d0-99a1-47f4-aa57-2722f6011f2a'],
                'debit',
                ['3274cc74-f7ab-40a4-984a-186a593401f7' => null,],
                null,
            ],
            [
                ['debit' => '3274cc74-f7ab-40a4-984a-186a593401f7  '],
                'debit',
                ['3274cc74-f7ab-40a4-984a-186a593401f7' => null,],
                '3274cc74-f7ab-40a4-984a-186a593401f7',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider forTestTrimDraftSlip
     */
    public function validateAndTrimDraftSlip_($slip_in, $result_expected, $callValidateDateFormat, $validateDateFormatResult)
    {
        $accounts = [
            '3274cc74-f7ab-40a4-984a-186a593401f7' => null,
            '471b26d0-99a1-47f4-aa57-2722f6011f2a' => null,
            '8548f227-7be9-42be-b5c7-66da432f5dad' => null,
            '90dc7df5-07ea-4086-9461-0555c2a9d03c' => null,
        ];
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        if ($callValidateDateFormat) {
            $BookKeepingMock->shouldReceive('validateDateFormat')
                ->once()
                ->with(trim($slip_in['date']))
                ->andReturn($validateDateFormatResult);
        }
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);

        $controller = new PostSlipsActionApi($BookKeepingMock, $responderMock);
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('validateAndTrimDraftSlip');
        $method->setAccessible(true);
        $result_actual = $method->invoke($controller, $slip_in, $accounts);

        $this->assertSame($result_expected, $result_actual);
    }

    public function forTestTrimDraftSlip()
    {
        return [
            [
                [],
                [
                    'success' => false,
                    'slip' => ['memo' => null],
                ],
                false,
                false,
            ],
            /*
            [
                ['outline' => ['  outline1']],
                [
                    'success' => false,
                    'slip' => ['memo' => null],
                ],
                false,
                false,
            ],
            [
                ['outline' => '  '],
                [
                    'success' => false,
                    'slip' => ['memo' => null],
                ],
                false,
                false,
            ],
            [
                ['outline' => '  outline1'],
                [
                    'success' => false,
                    'slip' => ['outline' => 'outline1',  'memo' => null],
                ],
                false,
                false,
            ],
            [
                ['outline' => ''],
                [
                    'success' => false,
                    'slip' => ['memo' => null],
                ],
                false,
                false,
            ],
            [
                ['outline' => '  '],
                [
                    'success' => false,
                    'slip' => ['memo' => null],
                ],
                false,
                false,
            ],
            [
                ['date' => '2020-01-01'],
                ['success' => false, 'slip' => ['date' => '2020-01-01', 'memo' => null]],
                true,
                true,
            ],
            [
                ['outline' => '  outline1'],
                ['outline' => 'outline1', 'entries' => [], 'memo' => null],
            ],
            [
                ['outline' => ['  outline1']],
                ['entries' => [], 'memo' => null],
            ],
            [
                ['date' => '  2020-01-31'],
                ['date' => '2020-01-31', 'entries' => [], 'memo' => null],
            ],
            [
                ['date' => ['  2020-01-31']],
                ['entries' => [], 'memo' => null],
            ],
            [
                ['entries' => 'entries1'],
                ['entries' => [], 'memo' => null],
            ],
            [
                ['entries' => ['entries2']],
                ['entries' => [], 'memo' => null],
            ],
            [
                ['entries' => ['entries2', []]],
                ['entries' => [], 'memo' => null],
            ],
            [
                ['entries' => [['entries3']]],
                ['entries' => [], 'memo' => null],
            ],
            [
                ['entries' => []],
                ['entries' => [], 'memo' => null],
            ],
            [
                ['entries' => [['dummy' => 'dummy1']]],
                ['entries' => [], 'memo' => null],
            ],
            [
                ['entries' => ['debit' => 'debit1', []]],
                ['entries' => [], 'memo' => null],
            ],
            [
                ['entries' => ['debit' => '  debit1', ['debit' => '  debit2']]],
                ['entries' => [['debit' => 'debit2']], 'memo' => null],
            ],
            [
                ['entries' => ['debit' => '  debit1', ['debit' => ['  debit2']]]],
                ['entries' => [], 'memo' => null],
            ],
            [
                ['entries' => [['debit' => '  debit1'], ['credit' => '  credit2', 'dummy' => 'dummy2']]],
                ['entries' => [['debit' => 'debit1'], ['credit' => 'credit2']], 'memo' => null],
            ],
            [
                ['entries' => [['debit' => '  debit1'], ['credit' => ['  credit2'], 'dummy' => 'dummy2']]],
                ['entries' => [['debit' => 'debit1']], 'memo' => null],
            ],
            [
                ['entries' => [['amount' => 1010]]],
                ['entries' => [['amount' => 1010]], 'memo' => null],
            ],
            [
                ['entries' => [['client' => '  client1']]],
                ['entries' => [['client' => 'client1']], 'memo' => null],
            ],
            [
                ['entries' => [['client' => ['  client1']]]],
                ['entries' => [], 'memo' => null],
            ],
            [
                ['entries' => [['outline' => '  outline1']]],
                ['entries' => [['outline' => 'outline1']], 'memo' => null],
            ],
            [
                ['entries' => [['outline' => ['  outline1']]]],
                ['entries' => [], 'memo' => null],
            ],
            [
                ['memo' => null],
                ['entries' => [], 'memo' => null],
            ],
            [
                ['memo' => ' '],
                ['entries' => [], 'memo' => null],
            ],
            [
                ['memo' => '  memo1'],
                ['entries' => [], 'memo' => 'memo1'],
            ],
            [
                ['memo' => ['  memo1']],
                ['entries' => [], 'memo' => null],
            ],
            [
                ['dummy' => 'dummy3'],
                ['entries' => [], 'memo' => null],
            ],
            [
                [
                    'dummy'   => 'dummyX',
                    'outline' => '  outlineX  ',
                    'date'    => '  2019-12-31  ',
                    'entries' => [
                        [
                            'debit' => 'debitXX  ',
                            'credit' => '  creditXX',
                            'amount' => 100,
                            'client' => '  clientXX  ',
                            'outline' => '  outlineXX  ',
                        ],
                        [
                            'debit' => '  debitYY',
                            'credit' => 'creditYY  ',
                            'amount' => 200,
                            'client' => '  clientYY',
                            'outline' => 'outlineYY  ',
                        ],
                        [
                            'dummy'   => 'dummyZZZ',
                            'credit' => 'creditZZ  ',
                            'amount' => 300,
                            'client' => '  ',
                            'outline' => '  ',
                        ],
                    ],
                    'memo'    => '    memo11',
                ],
                [
                    'outline' => 'outlineX',
                    'date'    => '2019-12-31',
                    'entries' => [
                        [
                            'debit' => 'debitXX',
                            'credit' => 'creditXX',
                            'amount' => 100,
                            'client' => 'clientXX',
                            'outline' => 'outlineXX',
                        ],
                        [
                            'debit' => 'debitYY',
                            'credit' => 'creditYY',
                            'amount' => 200,
                            'client' => 'clientYY',
                            'outline' => 'outlineYY',
                        ],
                        [
                            'credit' => 'creditZZ',
                            'amount' => 300,
                            'client' => '',
                            'outline' => '',
                        ],
                    ],
                    'memo' => 'memo11',
                ],
            ],
            */
        ];
    }
}
