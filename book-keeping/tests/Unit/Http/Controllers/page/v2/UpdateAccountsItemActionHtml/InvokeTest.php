<?php

namespace Tests\Unit\Http\Controllers\page\v2\UpdateAccountsItemActionHtml;

use App\Http\Controllers\page\v2\UpdateAccountsItemActionHtml;
use App\Http\Responder\page\v2\UpdateAccountsItemViewResponder;
use App\Service\AccountService;
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
        $accountsItemId = (string) Str::uuid();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($accountsItemId)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, null]);
        $serviceMock->shouldNotReceive('updateAccount');
        $serviceMock->shouldNotReceive('retrieveCategorizedAccounts');
        /** @var \App\Http\Responder\page\v2\UpdateAccountsItemViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(UpdateAccountsItemViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldNotReceive('isMethod');
        $requestMock->shouldNotReceive('input');
        $requestMock->shouldNotReceive('all');

        $controller = new UpdateAccountsItemActionHtml($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock, $bookId, $accountsItemId);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior_2(): void
    {
        $bookId = (string) Str::uuid();
        $accountsItemId = (string) Str::uuid();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($accountsItemId)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn([-1, null]);
        $serviceMock->shouldNotReceive('updateAccount');
        $serviceMock->shouldNotReceive('retrieveCategorizedAccounts');
        /** @var \App\Http\Responder\page\v2\UpdateAccountsItemViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(UpdateAccountsItemViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldNotReceive('isMethod');
        $requestMock->shouldNotReceive('input');
        $requestMock->shouldNotReceive('all');

        $controller = new UpdateAccountsItemActionHtml($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock, $bookId, $accountsItemId);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior_3(): void
    {
        $bookId = (string) Str::uuid();
        $accountsItemId = (string) Str::uuid();
        $accountsGroupId = (string) Str::uuid();
        $title = 'account_title_104';
        $description = 'account_description_105';
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($accountsItemId)
            ->andReturn(true);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($accountsGroupId)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, []]);
        $serviceMock->shouldReceive('updateAccount')
            ->once()
            ->with($accountsItemId, ['group' => $accountsGroupId, 'title' => $title, 'description' => $description, 'selectable' => false], $bookId)
            ->andReturn([-1, null]);
        $serviceMock->shouldNotReceive('retrieveCategorizedAccounts');
        /** @var \App\Http\Responder\page\v2\UpdateAccountsItemViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(UpdateAccountsItemViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('accountgroup')
            ->andReturn($accountsGroupId);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('title')
            ->andReturn($title);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('description')
            ->andReturn($description);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn([]);

        $controller = new UpdateAccountsItemActionHtml($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock, $bookId, $accountsItemId);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior_4(): void
    {
        $bookId = (string) Str::uuid();
        $accountsItemId = (string) Str::uuid();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($accountsItemId)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, []]);
        $serviceMock->shouldNotReceive('updateAccount');
        $serviceMock->shouldReceive('retrieveCategorizedAccounts')
            ->once()
            ->with(false, $bookId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, null]);
        /** @var \App\Http\Responder\page\v2\UpdateAccountsItemViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(UpdateAccountsItemViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(false);
        $requestMock->shouldNotReceive('input');
        $requestMock->shouldNotReceive('all');

        $controller = new UpdateAccountsItemActionHtml($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock, $bookId, $accountsItemId);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior_5(): void
    {
        $bookId = (string) Str::uuid();
        $accountsItemId = (string) Str::uuid();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($accountsItemId)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, []]);
        $serviceMock->shouldNotReceive('updateAccount');
        $serviceMock->shouldReceive('retrieveCategorizedAccounts')
            ->once()
            ->with(false, $bookId)
            ->andReturn([-1, null]);
        /** @var \App\Http\Responder\page\v2\UpdateAccountsItemViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(UpdateAccountsItemViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(false);
        $requestMock->shouldNotReceive('input');
        $requestMock->shouldNotReceive('all');

        $controller = new UpdateAccountsItemActionHtml($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock, $bookId, $accountsItemId);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }
}
