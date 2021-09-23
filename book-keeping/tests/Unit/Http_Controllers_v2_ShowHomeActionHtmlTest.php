<?php

namespace Tests\Unit;

use App\Http\Controllers\v2\ShowHomeActionHtml;
use App\Http\Responder\v2\ShowHomeViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Controllers_v2_ShowHomeActionHtmlTest extends TestCase
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
        $bookId = (string) Str::uuid();
        $context = [
            'book' => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
        ];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn($context['book']);
        /** @var \App\Http\Responder\v2\ShowHomeViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(ShowHomeViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new ShowHomeActionHtml($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $bookId);

        $this->assertSame($response_expected, $response_actual);
    }
}
