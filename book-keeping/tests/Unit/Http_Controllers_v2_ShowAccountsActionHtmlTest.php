<?php

namespace Tests\Unit;

use App\Http\Controllers\v2\ShowAccountsActionHtml;
use App\Http\Responder\v2\ShowAccountsViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Controllers_v2_ShowAccountsActionHtmlTest extends TestCase
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
            'accounts' => [
                'asset'     => ['groups' => []],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'book'     => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
        ];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn($context['book']);
        $BookKeepingMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with(false, $bookId)
            ->andReturn($context['accounts']);
        /** @var \App\Http\Responder\v2\ShowAccountsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(ShowAccountsViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new ShowAccountsActionHtml($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $bookId);

        $this->assertSame($response_expected, $response_actual);
    }
}
