<?php

namespace Tests\Unit;

use App\Http\Controllers\ShowDashboardActionHtml;
use App\Http\Responder\ShowDashboardViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Controllers_ShowDashboardActionHtmlTest extends TestCase
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
        $context['books'] = [
            ['id' => (string) Str::uuid(), 'owner' => 'owner', 'name' => 'name']
        ];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveAvailableBook')
            ->once()
            ->andReturn($context['books']);
        /** @var \App\Http\Responder\ShowDashboardViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(ShowDashboardViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new ShowDashboardActionHtml($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }
}
