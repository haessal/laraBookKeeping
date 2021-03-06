<?php

namespace Tests\Unit;

use App\Http\Controllers\v1\ShowStatementsActionHTML;
use App\Http\Responder\v1\ShowStatementsViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class Http_Controllers_v1_ShowStatementsActionHTMLTest extends TestCase
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
        $beginning_date = '2019-12-01';
        $end_date = '2019-12-31';
        $end_date_of_previous_period = date('Y-m-d', strtotime($beginning_date) - 86400);
        $context = [
            'beginning_date' => $beginning_date,
            'end_date'       => $end_date,
            'statements'     => [
                'income_statement_item1' => ['amount' => 11, 'groups' => []],
                'income_statement_item2' => ['amount' => 12, 'groups' => []],
                'income_statement_item3' => ['amount' => 13, 'groups' => []],
            ],
            'previous_balance_sheet' => [
                'previous_balance_sheet_itme1' => ['amount' => 11, 'groups' => []],
                'previous_balance_sheet_itme2' => ['amount' => 21, 'groups' => []],
                'previous_balance_sheet_itme3' => ['amount' => 31, 'groups' => []],
            ],
            'balance_sheet' => [
                'balance_sheet_itme1' => ['amount' => 11, 'groups' => []],
                'balance_sheet_itme2' => ['amount' => 21, 'groups' => []],
                'balance_sheet_itme3' => ['amount' => 31, 'groups' => []],
            ],
            'slips' => [
                'slips_itme1' => [
                    'date'         => 'date1',
                    'slip_outline' => 'slip_outline1',
                    'slip_memo'    => 'slip_memo1',
                    'items'        => [],
                ],
            ],
            'message'            => null,
            'display_statements' => true,
        ];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('validatePeriod')
            ->once()
            ->with($beginning_date, $end_date)
            ->andReturn(true);
        $BookKeepingMock->shouldReceive('retrieveStatements')
            ->once()
            ->with($beginning_date, $end_date)
            ->andReturn($context['statements']);
        $BookKeepingMock->shouldReceive('retrieveStatements')
            ->once()
            ->with('1970-01-01', $end_date_of_previous_period)
            ->andReturn($context['previous_balance_sheet']);
        $BookKeepingMock->shouldReceive('retrieveStatements')
            ->once()
            ->with('1970-01-01', $end_date)
            ->andReturn($context['balance_sheet']);
        $BookKeepingMock->shouldReceive('retrieveSlips')
            ->once()
            ->with($beginning_date, $end_date, null, null, null, null)
            ->andReturn($context['slips']);
        /** @var \App\Http\Responder\v1\ShowStatementsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(ShowStatementsViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('BEGINNING')
            ->andReturn($beginning_date);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('END')
            ->andReturn($end_date);

        $controller = new ShowStatementsActionHTML($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseWithEmptyBeginningDateRequest()
    {
        $beginning_date = '';
        $end_date = '2019-11-30';
        $context = [
            'beginning_date'     => $beginning_date,
            'end_date'           => $end_date,
            'message'            => __('There is no item to be shown.'),
            'display_statements' => false,
        ];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldNotReceive('validatePeriod');
        $BookKeepingMock->shouldNotReceive('retrieveStatements');
        $BookKeepingMock->shouldNotReceive('retrieveSlips');
        /** @var \App\Http\Responder\v1\ShowStatementsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(ShowStatementsViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('BEGINNING')
            ->andReturn($beginning_date);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('END')
            ->andReturn($end_date);

        $controller = new ShowStatementsActionHTML($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseWithEmptyEndDateRequest()
    {
        $beginning_date = '2019-10-01';
        $end_date = '';
        $context = [
            'beginning_date'     => $beginning_date,
            'end_date'           => $end_date,
            'message'            => __('There is no item to be shown.'),
            'display_statements' => false,
        ];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldNotReceive('validatePeriod');
        $BookKeepingMock->shouldNotReceive('retrieveStatements');
        $BookKeepingMock->shouldNotReceive('retrieveSlips');
        /** @var \App\Http\Responder\v1\ShowStatementsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(ShowStatementsViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('BEGINNING')
            ->andReturn($beginning_date);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('END')
            ->andReturn($end_date);

        $controller = new ShowStatementsActionHTML($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseWithInvalidDateRequest()
    {
        $beginning_date = '2019-09-30';
        $end_date = '2019-09-01';
        $context = [
            'beginning_date'     => $beginning_date,
            'end_date'           => $end_date,
            'message'            => __('There is no item to be shown.'),
            'display_statements' => false,
        ];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldNotReceive('validatePeriod')
            ->once()
            ->with($beginning_date, $end_date)
            ->andReturn(false);
        $BookKeepingMock->shouldNotReceive('retrieveStatements');
        $BookKeepingMock->shouldNotReceive('retrieveSlips');
        /** @var \App\Http\Responder\v1\ShowStatementsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(ShowStatementsViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('BEGINNING')
            ->andReturn($beginning_date);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('END')
            ->andReturn($end_date);

        $controller = new ShowStatementsActionHTML($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseWithNoDateRequest()
    {
        $today = date('Y-m-d');
        $beginning_date = $today;
        $end_date = $today;
        $end_date_of_previous_period = date('Y-m-d', strtotime($beginning_date) - 86400);
        $context = [
            'beginning_date' => $beginning_date,
            'end_date'       => $end_date,
            'statements'     => [
                'income_statement_item1' => ['amount' => 11, 'groups' => []],
                'income_statement_item2' => ['amount' => 12, 'groups' => []],
                'income_statement_item3' => ['amount' => 13, 'groups' => []],
            ],
            'previous_balance_sheet' => [
                'previous_balance_sheet_itme1' => ['amount' => 11, 'groups' => []],
                'previous_balance_sheet_itme2' => ['amount' => 21, 'groups' => []],
                'previous_balance_sheet_itme3' => ['amount' => 31, 'groups' => []],
            ],
            'balance_sheet' => [
                'balance_sheet_itme1' => ['amount' => 11, 'groups' => []],
                'balance_sheet_itme2' => ['amount' => 21, 'groups' => []],
                'balance_sheet_itme3' => ['amount' => 31, 'groups' => []],
            ],
            'slips' => [
                'slips_itme1' => [
                    'date'         => 'date1',
                    'slip_outline' => 'slip_outline1',
                    'slip_memo'    => 'slip_memo1',
                    'items'        => [],
                ],
            ],
            'message'            => null,
            'display_statements' => true,
        ];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('validatePeriod')
            ->once()
            ->with($beginning_date, $end_date)
            ->andReturn(true);
        $BookKeepingMock->shouldReceive('retrieveStatements')
            ->once()
            ->with($beginning_date, $end_date)
            ->andReturn($context['statements']);
        $BookKeepingMock->shouldReceive('retrieveStatements')
            ->once()
            ->with('1970-01-01', $end_date_of_previous_period)
            ->andReturn($context['previous_balance_sheet']);
        $BookKeepingMock->shouldReceive('retrieveStatements')
            ->once()
            ->with('1970-01-01', $end_date)
            ->andReturn($context['balance_sheet']);
        $BookKeepingMock->shouldReceive('retrieveSlips')
            ->once()
            ->with($beginning_date, $end_date, null, null, null, null)
            ->andReturn($context['slips']);
        /** @var \App\Http\Responder\v1\ShowStatementsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(ShowStatementsViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(false);

        $controller = new ShowStatementsActionHTML($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }
}
