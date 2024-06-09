<?php

namespace Tests\Unit\Http\Controllers\page\v2\UpdateAccountsGroupActionHtml;

use App\Http\Controllers\page\v2\UpdateAccountsGroupActionHtml;
use App\Http\Responder\page\v2\UpdateAccountsGroupViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mockery;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class InvokeTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior_1(): void
    {
        $bookId = (string) Str::uuid();
        $accountsGroupId = (string) Str::uuid();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($accountsGroupId)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, null]);
        $serviceMock->shouldNotReceive('updateAccountGroup');
        $serviceMock->shouldNotReceive('retrieveCategorizedAccounts');
        /** @var \App\Http\Responder\page\v2\UpdateAccountsGroupViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(UpdateAccountsGroupViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldNotReceive('isMethod');
        $requestMock->shouldNotReceive('input');
        $requestMock->shouldNotReceive('all');

        $controller = new UpdateAccountsGroupActionHtml($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock, $bookId, $accountsGroupId);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior_2(): void
    {
        $bookId = (string) Str::uuid();
        $accountsGroupId = (string) Str::uuid();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($accountsGroupId)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn([-1, null]);
        $serviceMock->shouldNotReceive('updateAccountGroup');
        $serviceMock->shouldNotReceive('retrieveCategorizedAccounts');
        /** @var \App\Http\Responder\page\v2\UpdateAccountsGroupViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(UpdateAccountsGroupViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldNotReceive('isMethod');
        $requestMock->shouldNotReceive('input');
        $requestMock->shouldNotReceive('all');

        $controller = new UpdateAccountsGroupActionHtml($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock, $bookId, $accountsGroupId);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior_3(): void
    {
        $bookId = (string) Str::uuid();
        $accountsGroupId = (string) Str::uuid();
        $accountsGroupTitle = 'account_group_title_103';
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($accountsGroupId)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, []]);
        $serviceMock->shouldReceive('updateAccountGroup')
            ->once()
            ->with($accountsGroupId, ['title' => $accountsGroupTitle, 'is_current' => false], $bookId)
            ->andReturn([-1, null]);
        $serviceMock->shouldNotReceive('retrieveCategorizedAccounts');
        /** @var \App\Http\Responder\page\v2\UpdateAccountsGroupViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(UpdateAccountsGroupViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('title')
            ->andReturn($accountsGroupTitle);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn([]);

        $controller = new UpdateAccountsGroupActionHtml($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock, $bookId, $accountsGroupId);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior_4(): void
    {
        $bookId = (string) Str::uuid();
        $accountsGroupId = (string) Str::uuid();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($accountsGroupId)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, []]);
        $serviceMock->shouldNotReceive('updateAccountGroup');
        $serviceMock->shouldReceive('retrieveCategorizedAccounts')
            ->once()
            ->with(false, $bookId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, null]);
        /** @var \App\Http\Responder\page\v2\UpdateAccountsGroupViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(UpdateAccountsGroupViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(false);
        $requestMock->shouldNotReceive('input');
        $requestMock->shouldNotReceive('all');

        $controller = new UpdateAccountsGroupActionHtml($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock, $bookId, $accountsGroupId);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior_5(): void
    {
        $bookId = (string) Str::uuid();
        $accountsGroupId = (string) Str::uuid();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($accountsGroupId)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, []]);
        $serviceMock->shouldNotReceive('updateAccountGroup');
        $serviceMock->shouldReceive('retrieveCategorizedAccounts')
            ->once()
            ->with(false, $bookId)
            ->andReturn([-1, null]);
        /** @var \App\Http\Responder\page\v2\UpdateAccountsGroupViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(UpdateAccountsGroupViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(false);
        $requestMock->shouldNotReceive('input');
        $requestMock->shouldNotReceive('all');

        $controller = new UpdateAccountsGroupActionHtml($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock, $bookId, $accountsGroupId);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }
}
