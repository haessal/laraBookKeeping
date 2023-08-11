<?php

namespace Tests\Unit\Http\Controllers\api\v1\PutBooksPermissionsActionApi;

use App\Http\Controllers\api\v1\PutBooksPermissionsActionApi;
use App\Http\Responder\api\v1\BookAccessPermissionListJsonResponder;
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

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior(): void
    {
        $bookId = (string) Str::uuid();
        $userName = 'user24';
        $mode = 'ReadWrite';
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('authorizeToAccess')
            ->once()
            ->with($bookId, $userName, $mode)
            ->andReturn([-1, null]);
        /** @var \App\Http\Responder\api\v1\BookAccessPermissionListJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(BookAccessPermissionListJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn(['user' => $userName, 'permitted_to' => $mode]);

        $controller = new PutBooksPermissionsActionApi($serviceMock, $responderMock);
        $response = $controller->__invoke($requestMock, $bookId);

        $this->assertSame(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function test_it_returns_internal_server_error_without_book_from_bookkeeping_service(): void
    {
        $bookId = (string) Str::uuid();
        $userName = 'user54';
        $mode = 'ReadOnly';
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('authorizeToAccess')
            ->once()
            ->with($bookId, $userName, $mode)
            ->andReturn([BookKeepingService::STATUS_NORMAL, null]);
        $serviceMock->shouldReceive('retrievePermittedUsers')
            ->once()
            ->with($bookId)
            ->andReturn([-1, []]);
        /** @var \App\Http\Responder\api\v1\BookAccessPermissionListJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(BookAccessPermissionListJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn(['user' => $userName, 'permitted_to' => $mode]);

        $controller = new PutBooksPermissionsActionApi($serviceMock, $responderMock);
        $response = $controller->__invoke($requestMock, $bookId);

        $this->assertSame(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}
