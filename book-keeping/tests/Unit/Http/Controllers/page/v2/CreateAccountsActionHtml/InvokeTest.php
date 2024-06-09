<?php

namespace Tests\Unit\Http\Controllers\page\v2\CreateAccountsActionHtml;

use App\Http\Controllers\page\v2\CreateAccountsActionHtml;
use App\Http\Responder\page\v2\CreateAccountsViewResponder;
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
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, null]);
        $serviceMock->shouldNotReceive('createAccountGroup');
        $serviceMock->shouldNotReceive('createAccount');
        $serviceMock->shouldNotReceive('retrieveCategorizedAccounts');
        /** @var \App\Http\Responder\page\v2\CreateAccountsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateAccountsViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldNotReceive('isMethod');
        $requestMock->shouldNotReceive('input');
        $requestMock->shouldNotReceive('all');

        $controller = new CreateAccountsActionHtml($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock, $bookId);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior_2(): void
    {
        $bookId = (string) Str::uuid();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn([-1, null]);
        $serviceMock->shouldNotReceive('createAccountGroup');
        $serviceMock->shouldNotReceive('createAccount');
        $serviceMock->shouldNotReceive('retrieveCategorizedAccounts');
        /** @var \App\Http\Responder\page\v2\CreateAccountsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateAccountsViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldNotReceive('isMethod');
        $requestMock->shouldNotReceive('input');
        $requestMock->shouldNotReceive('all');

        $controller = new CreateAccountsActionHtml($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock, $bookId);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior_3(): void
    {
        $bookId = (string) Str::uuid();
        $accountType = AccountService::ACCOUNT_TYPE_ASSET;
        $accountGroupTitle = 'account_group_title_95';
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, []]);
        $serviceMock->shouldReceive('createAccountGroup')
            ->once()
            ->with($accountType, $accountGroupTitle, $bookId)
            ->andReturn([-1, null]);
        $serviceMock->shouldNotReceive('createAccount');
        $serviceMock->shouldNotReceive('retrieveCategorizedAccounts');
        /** @var \App\Http\Responder\page\v2\CreateAccountsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateAccountsViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('create')
            ->andReturn('group');
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn([
                'accounttype' => $accountType,
                'title' => $accountGroupTitle,
            ]);

        $controller = new CreateAccountsActionHtml($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock, $bookId);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior_4(): void
    {
        $bookId = (string) Str::uuid();
        $accountGroupId = (string) Str::uuid();
        $accountItemTitle = 'account_item_title_145';
        $description = 'description_146';
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, []]);
        $serviceMock->shouldNotReceive('createAccountGroup');
        $serviceMock->shouldReceive('createAccount')
            ->once()
            ->with($accountGroupId, $accountItemTitle, $description, $bookId)
            ->andReturn([-1, null]);
        $serviceMock->shouldNotReceive('retrieveCategorizedAccounts');
        /** @var \App\Http\Responder\page\v2\CreateAccountsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateAccountsViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('create')
            ->andReturn('item');
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn([
                'accountgroup' => $accountGroupId,
                'title' => $accountItemTitle,
                'description' => $description,
            ]);

        $controller = new CreateAccountsActionHtml($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock, $bookId);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior_5(): void
    {
        $bookId = (string) Str::uuid();
        $accountType = AccountService::ACCOUNT_TYPE_ASSET;
        $accountGroupTitle = 'account_group_title_95';
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, []]);
        $serviceMock->shouldReceive('createAccountGroup')
            ->once()
            ->with($accountType, $accountGroupTitle, $bookId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, null]);
        $serviceMock->shouldNotReceive('createAccount');
        $serviceMock->shouldReceive('retrieveCategorizedAccounts')
            ->once()
            ->with(false, $bookId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, null]);
        /** @var \App\Http\Responder\page\v2\CreateAccountsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateAccountsViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('create')
            ->andReturn('group');
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn([
                'accounttype' => $accountType,
                'title' => $accountGroupTitle,
            ]);

        $controller = new CreateAccountsActionHtml($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock, $bookId);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }

    public function test_it_returns_internal_server_error_with_unexpected_bookkeeping_service_behavior_6(): void
    {
        $bookId = (string) Str::uuid();
        $accountType = AccountService::ACCOUNT_TYPE_ASSET;
        $accountGroupTitle = 'account_group_title_95';
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $serviceMock */
        $serviceMock = Mockery::mock(BookKeepingService::class);
        $serviceMock->shouldReceive('validateUuid')
            ->once()
            ->with($bookId)
            ->andReturn(true);
        $serviceMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, []]);
        $serviceMock->shouldReceive('createAccountGroup')
            ->once()
            ->with($accountType, $accountGroupTitle, $bookId)
            ->andReturn([BookKeepingService::STATUS_NORMAL, null]);
        $serviceMock->shouldNotReceive('createAccount');
        $serviceMock->shouldReceive('retrieveCategorizedAccounts')
            ->once()
            ->with(false, $bookId)
            ->andReturn([-1, null]);
        /** @var \App\Http\Responder\page\v2\CreateAccountsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateAccountsViewResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('create')
            ->andReturn('group');
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn([
                'accounttype' => $accountType,
                'title' => $accountGroupTitle,
            ]);

        $controller = new CreateAccountsActionHtml($serviceMock, $responderMock);
        try {
            $response = $controller->__invoke($requestMock, $bookId);
            $this->fail('Expected HttpException was not thrown');
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getStatusCode());
        }
    }
}
