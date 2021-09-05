<?php

namespace Tests\Unit;

use App\Http\Controllers\v2\UpdateAccountsItemActionHtml;
use App\Http\Responder\v2\UpdateAccountsItemViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Controllers_v2_UpdateAccountsItemActionHtmlTest extends TestCase
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
        $accountsItemId = (string) Str::uuid();
        $title = 'title29';
        $description = 'description30';
        $bk_code = 1101;
        $context = [
            'book'           => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
            'accounts'       => [
                'asset'     => ['groups' => [
                    $accountsGroupId => ['items' => [
                        $accountsItemId => [
                            'title'       => $title,
                            'description' => $description,
                            'selectable'  => true,
                            'bk_code'     => $bk_code,
                        ],
                    ]],
                ]],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'accounttypekey' => 'asset',
            'accountsitem'   => [
                'id'                   => $accountsItemId,
                'type'                 => __('Assets'),
                'groupid'              => $accountsGroupId,
                'title'                => $title,
                'description'          => $description,
                'attribute_selectable' => 'checked',
                'bk_code'              => $bk_code,
            ],
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
        /** @var \App\Http\Responder\v2\UpdateAccountsItemViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(UpdateAccountsItemViewResponder::class);
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

        $controller = new UpdateAccountsItemActionHtml($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $bookId, $accountsItemId);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTForUpdateRequestWithoutSelectable()
    {
        $bookId = (string) Str::uuid();
        $accountsGroupId = (string) Str::uuid();
        $accountsItemId = (string) Str::uuid();
        $title = 'title98';
        $description = 'description99';
        $bk_code = 1102;
        $context = [
            'book'           => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
            'accounts'       => [
                'asset'     => ['groups' => [
                    $accountsGroupId => ['items' => [
                        $accountsItemId => [
                            'title'       => $title,
                            'description' => $description,
                            'selectable'  => false,
                            'bk_code'     => $bk_code,
                        ],
                    ]],
                ]],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'accounttypekey' => 'asset',
            'accountsitem'   => [
                'id'                   => $accountsItemId,
                'type'                 => __('Assets'),
                'groupid'              => $accountsGroupId,
                'title'                => $title,
                'description'          => $description,
                'attribute_selectable' => null,
                'bk_code'              => $bk_code,
            ],
        ];
        $request = [
            'accountgroup' => $accountsGroupId,
            'title'        => 'title131',
            'description'  => 'description132',
        ];
        $newData = ['group' => $accountsGroupId, 'title' => $request['title'], 'description' => $request['description'], 'selectable' => false];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('updateAccount')
            ->once()
            ->with($accountsItemId, $newData, $bookId);
        $BookKeepingMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn($context['book']);
        $BookKeepingMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with(false, $bookId)
            ->andReturn($context['accounts']);
        /** @var \App\Http\Responder\v2\UpdateAccountsItemViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(UpdateAccountsItemViewResponder::class);
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
            ->with('accountgroup')
            ->andReturn($request['accountgroup']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('title')
            ->andReturn($request['title']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('description')
            ->andReturn($request['description']);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn($request);

        $controller = new UpdateAccountsItemActionHtml($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $bookId, $accountsItemId);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTForUpdateRequestWithSelectable()
    {
        $bookId = (string) Str::uuid();
        $accountsGroupId = (string) Str::uuid();
        $accountsItemId = (string) Str::uuid();
        $title = 'title191';
        $description = 'description192';
        $bk_code = 1103;
        $context = [
            'book'           => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
            'accounts'       => [
                'asset'     => ['groups' => [
                    $accountsGroupId => ['items' => [
                        $accountsItemId => [
                            'title'       => $title,
                            'description' => $description,
                            'selectable'  => true,
                            'bk_code'     => $bk_code,
                        ],
                    ]],
                ]],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'accounttypekey' => 'asset',
            'accountsitem'   => [
                'id'                   => $accountsItemId,
                'type'                 => __('Assets'),
                'groupid'              => $accountsGroupId,
                'title'                => $title,
                'description'          => $description,
                'attribute_selectable' => 'checked',
                'bk_code'              => $bk_code,
            ],
        ];
        $request = [
            'accountgroup'         => $accountsGroupId,
            'title'                => 'title224',
            'description'          => 'description225',
            'attribute_selectable' => '1',
        ];
        $newData = ['group' => $accountsGroupId, 'title' => $request['title'], 'description' => $request['description'], 'selectable' => true];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('updateAccount')
            ->once()
            ->with($accountsItemId, $newData, $bookId);
        $BookKeepingMock->shouldReceive('retrieveBookInformation')
            ->once()
            ->with($bookId)
            ->andReturn($context['book']);
        $BookKeepingMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with(false, $bookId)
            ->andReturn($context['accounts']);
        /** @var \App\Http\Responder\v2\UpdateAccountsItemViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(UpdateAccountsItemViewResponder::class);
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
            ->with('accountgroup')
            ->andReturn($request['accountgroup']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('title')
            ->andReturn($request['title']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('description')
            ->andReturn($request['description']);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn($request);

        $controller = new UpdateAccountsItemActionHtml($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $bookId, $accountsItemId);

        $this->assertSame($response_expected, $response_actual);
    }
}
