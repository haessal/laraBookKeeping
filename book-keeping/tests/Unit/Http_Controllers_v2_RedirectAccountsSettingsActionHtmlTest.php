<?php

namespace Tests\Unit;

use App\Http\Controllers\v2\RedirectAccountsSettingsActionHtml;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Controllers_v2_RedirectAccountsSettingsActionHtmlTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function __invoke_ReturnRedirectResponseToAccountsGroupsSettings()
    {
        $bookId = (string) Str::uuid();
        $accountGroupId = (string) Str::uuid();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveBookInfomation')
            ->once()
            ->with($bookId)
            ->andReturn(['id' => $bookId]);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('accountsgroup')
            ->andReturn($accountGroupId);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('accountsitem')
            ->andReturn(null);

        $controller = new RedirectAccountsSettingsActionHtml($BookKeepingMock);
        $response = $controller->__invoke($requestMock, $bookId);

        $this->assertSame(route('v2_accounts_groups', ['bookId' => $bookId, 'accountsGroupId' => $accountGroupId]), $response->getTargetUrl());
    }

    /**
     * @test
     */
    public function __invoke_ReturnRedirectResponseToAccountsItemsSettings()
    {
        $bookId = (string) Str::uuid();
        $accountId = (string) Str::uuid();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveBookInfomation')
            ->once()
            ->with($bookId)
            ->andReturn(['id' => $bookId]);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('accountsgroup')
            ->andReturn(null);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('accountsitem')
            ->andReturn($accountId);

        $controller = new RedirectAccountsSettingsActionHtml($BookKeepingMock);
        $response = $controller->__invoke($requestMock, $bookId);

        $this->assertSame(route('v2_accounts_items', ['bookId' => $bookId, 'accountsItemId' => $accountId]), $response->getTargetUrl());
    }

    /**
     * @test
     */
    public function __invoke_ReturnRedirectResponseToAccountsSettings()
    {
        $bookId = (string) Str::uuid();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveBookInfomation')
            ->once()
            ->with($bookId)
            ->andReturn(['id' => $bookId]);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('accountsgroup')
            ->andReturn(null);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('accountsitem')
            ->andReturn(null);

        $controller = new RedirectAccountsSettingsActionHtml($BookKeepingMock);
        $response = $controller->__invoke($requestMock, $bookId);

        $this->assertSame(route('v2_accounts_settings', ['bookId' => $bookId]), $response->getTargetUrl());
    }
}
