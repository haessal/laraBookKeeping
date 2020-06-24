<?php

namespace Tests\Unit;

use App\Http\Controllers\v2\UpdateAccountsGroupActionHtml;
use App\Http\Responder\v2\UpdateAccountsGroupViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Controllers_v2_UpdateAccountsGroupActionHtmlTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandleGET()
    {
        $bookId = (string) Str::uuid();
        $accountsGroupId = (string) Str::uuid();
        $title = 'title28';
        $bk_code = 1100;
        $context = [
            'book'          => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
            'accounts'      => [
                'asset'     => ['groups' => [
                    $accountsGroupId => [
                        'title'     => $title,
                        'isCurrent' => true,
                        'bk_code'   => $bk_code,
                    ],
                ]],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'accountsgroup' => [
                'id'                => $accountsGroupId,
                'type'              => __('Assets'),
                'title'             => $title,
                'attribute_current' => 'checked',
                'bk_code'           => $bk_code,
            ],
        ];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveBookInfomation')
            ->once()
            ->with($bookId)
            ->andReturn($context['book']);
        $BookKeepingMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with(false, $bookId)
            ->andReturn($context['accounts']);
        /** @var \App\Http\Responder\v2\UpdateAccountsGroupViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(UpdateAccountsGroupViewResponder::class);
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

        $controller = new UpdateAccountsGroupActionHtml($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $bookId, $accountsGroupId);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTForUpdateRequestWithoutCurrent()
    {
        $bookId = (string) Str::uuid();
        $accountsGroupId = (string) Str::uuid();
        $title = 'title89';
        $bk_code = 1200;
        $context = [
            'book'          => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
            'accounts'      => [
                'asset'     => ['groups' => [
                    $accountsGroupId => [
                        'title'     => $title,
                        'isCurrent' => false,
                        'bk_code'   => $bk_code,
                    ],
                ]],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'accountsgroup' => [
                'id'                => $accountsGroupId,
                'type'              => __('Assets'),
                'title'             => $title,
                'attribute_current' => null,
                'bk_code'           => $bk_code,
            ],
        ];
        $request = [
            'title' => 'title114',
        ];
        $newData = ['title' => $request['title'], 'is_current' => false];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('updateAccountGroup')
            ->once()
            ->with($accountsGroupId, $newData, $bookId);
        $BookKeepingMock->shouldReceive('retrieveBookInfomation')
            ->once()
            ->with($bookId)
            ->andReturn($context['book']);
        $BookKeepingMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with(false, $bookId)
            ->andReturn($context['accounts']);
        /** @var \App\Http\Responder\v2\UpdateAccountsGroupViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(UpdateAccountsGroupViewResponder::class);
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
            ->with('title')
            ->andReturn($request['title']);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn($request);

        $controller = new UpdateAccountsGroupActionHtml($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $bookId, $accountsGroupId);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTForUpdateRequestWithCurrent()
    {
        $bookId = (string) Str::uuid();
        $accountsGroupId = (string) Str::uuid();
        $title = 'title164';
        $bk_code = 1300;
        $context = [
            'book'          => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
            'accounts'      => [
                'asset'     => ['groups' => [
                    $accountsGroupId => [
                        'title'     => $title,
                        'isCurrent' => true,
                        'bk_code'   => $bk_code,
                    ],
                ]],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'accountsgroup' => [
                'id'                => $accountsGroupId,
                'type'              => __('Assets'),
                'title'             => $title,
                'attribute_current' => 'checked',
                'bk_code'           => $bk_code,
            ],
        ];
        $request = [
            'title'             => 'title189',
            'attribute_current' => '1',
        ];
        $newData = ['title' => $request['title'], 'is_current' => true];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('updateAccountGroup')
            ->once()
            ->with($accountsGroupId, $newData, $bookId);
        $BookKeepingMock->shouldReceive('retrieveBookInfomation')
            ->once()
            ->with($bookId)
            ->andReturn($context['book']);
        $BookKeepingMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with(false, $bookId)
            ->andReturn($context['accounts']);
        /** @var \App\Http\Responder\v2\UpdateAccountsGroupViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(UpdateAccountsGroupViewResponder::class);
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
            ->with('title')
            ->andReturn($request['title']);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn($request);

        $controller = new UpdateAccountsGroupActionHtml($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $bookId, $accountsGroupId);

        $this->assertSame($response_expected, $response_actual);
    }
}
