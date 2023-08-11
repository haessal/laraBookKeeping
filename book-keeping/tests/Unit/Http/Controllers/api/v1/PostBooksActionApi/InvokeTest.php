<?php

namespace Tests\Unit\Http\Controllers\api\v1\PostBooksActionApi;

use App\Http\Controllers\api\v1\PostBooksActionApi;
use App\Http\Responder\api\v1\BookJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class InvokeTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_returns_internal_server_error_without_book_from_bookkeeping_service(): void
    {
        $title = 'BookTitle28';
        $bookId = (string) Str::uuid();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('createBook')
            ->once()
            ->with($title)
            ->andReturn($bookId);
        $serviceMock->shouldReceive('retrieveBook')
            ->once()
            ->with($bookId)
            ->andReturn(null);
        /** @var \App\Http\Responder\api\v1\BookJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(BookJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn(['name' => $title]);

        $controller = new PostBooksActionApi($serviceMock, $responderMock);
        $response = $controller->__invoke($requestMock);

        $this->assertSame(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}
