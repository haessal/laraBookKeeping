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
     * @dataProvider forTestTrimDraftSlip
     */
    public function trimDraftSlip_($slip_in, $slip_expected)
    {
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);

        $controller = new PostSlipsActionApi($BookKeepingMock, $responderMock);
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('trimDraftSlip');
        $method->setAccessible(true);
        $slip_actual = $method->invoke($controller, $slip_in);

        $this->assertSame($slip_expected, $slip_actual);
    }

    public function forTestTrimDraftSlip()
    {
        return [
            [
                [],
                ['entries' => [], 'memo' => null],
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
        ];
    }
}
