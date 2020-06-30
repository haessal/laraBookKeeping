<?php

namespace Tests\Unit;

use App\Http\Controllers\v2\CreateAccountsActionHtml;
use App\Http\Responder\v2\CreateAccountsViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Controllers_v2_CreateAccountsActionHtmlTest extends TestCase
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
        $context = [
            'book'          => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
            'accounts'      => [
                'asset'     => ['groups' => []],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'accounttype'   => null,
            'accountcreate' => [
                'grouptitle'  => null,
                'groupid'     => null,
                'itemtitle'   => null,
                'description' => null,
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
        /** @var \App\Http\Responder\v2\CreateAccountsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateAccountsViewResponder::class);
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

        $controller = new CreateAccountsActionHtml($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $bookId);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTForCreateGroup()
    {
        $bookId = (string) Str::uuid();
        $context = [
            'book'          => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
            'accounts'      => [
                'asset'     => ['groups' => []],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'accounttype'   => null,
            'accountcreate' => [
                'grouptitle'  => null,
                'groupid'     => null,
                'itemtitle'   => null,
                'description' => null,
            ],
        ];
        $request = [
            'create'      => 'group',
            'accounttype' => 'revenue',
            'title'       => ' title98 ',
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
        $BookKeepingMock->shouldReceive('createAccountGroup')
            ->once()
            ->with($request['accounttype'], trim($request['title']), $bookId);
        /** @var \App\Http\Responder\v2\CreateAccountsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateAccountsViewResponder::class);
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
            ->with('create')
            ->andReturn($request['create']);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn($request);

        $controller = new CreateAccountsActionHtml($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $bookId);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTForCreateGroupWithEmptyTitle()
    {
        $bookId = (string) Str::uuid();
        $context = [
            'book'          => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
            'accounts'      => [
                'asset'     => ['groups' => []],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'accounttype'   => 'asset',
            'accountcreate' => [
                'grouptitle'  => null,
                'groupid'     => null,
                'itemtitle'   => null,
                'description' => null,
            ],
        ];
        $request = [
            'create'      => 'group',
            'accounttype' => 'asset',
            'title'       => '  ',
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
        $BookKeepingMock->shouldNotReceive('createAccountGroup');
        /** @var \App\Http\Responder\v2\CreateAccountsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateAccountsViewResponder::class);
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
            ->with('create')
            ->andReturn($request['create']);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn($request);

        $controller = new CreateAccountsActionHtml($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $bookId);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTForCreateGroupWithInvalidType()
    {
        $bookId = (string) Str::uuid();
        $context = [
            'book'          => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
            'accounts'      => [
                'asset'     => ['groups' => []],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'accounttype'   => null,
            'accountcreate' => [
                'grouptitle'  => 'title230',
                'groupid'     => null,
                'itemtitle'   => null,
                'description' => null,
            ],
        ];
        $request = [
            'create'      => 'group',
            'accounttype' => 'accounttype',
            'title'       => ' title230 ',
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
        $BookKeepingMock->shouldNotReceive('createAccountGroup');
        /** @var \App\Http\Responder\v2\CreateAccountsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateAccountsViewResponder::class);
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
            ->with('create')
            ->andReturn($request['create']);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn($request);

        $controller = new CreateAccountsActionHtml($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $bookId);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTForCreateGroupWithoutType()
    {
        $bookId = (string) Str::uuid();
        $context = [
            'book'          => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
            'accounts'      => [
                'asset'     => ['groups' => []],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'accounttype'   => null,
            'accountcreate' => [
                'grouptitle'  => 'title294',
                'groupid'     => null,
                'itemtitle'   => null,
                'description' => null,
            ],
        ];
        $request = [
            'create'      => 'group',
            'title'       => ' title294 ',
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
        $BookKeepingMock->shouldNotReceive('createAccountGroup');
        /** @var \App\Http\Responder\v2\CreateAccountsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateAccountsViewResponder::class);
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
            ->with('create')
            ->andReturn($request['create']);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn($request);

        $controller = new CreateAccountsActionHtml($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $bookId);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTForCreateItem()
    {
        $bookId = (string) Str::uuid();
        $accountGroupId = (string) Str::uuid();
        $context = [
            'book'          => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
            'accounts'      => [
                'asset'     => ['groups' => []],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'accounttype'   => null,
            'accountcreate' => [
                'grouptitle'  => null,
                'groupid'     => null,
                'itemtitle'   => null,
                'description' => null,
            ],
        ];
        $request = [
            'create'       => 'item',
            'accountgroup' => $accountGroupId,
            'title'        => ' title360 ',
            'description'  => ' description361 ',
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
        $BookKeepingMock->shouldReceive('createAccount')
            ->once()
            ->with($request['accountgroup'], trim($request['title']), trim($request['description']), $bookId);
        /** @var \App\Http\Responder\v2\CreateAccountsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateAccountsViewResponder::class);
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
            ->with('create')
            ->andReturn($request['create']);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn($request);

        $controller = new CreateAccountsActionHtml($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $bookId);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTForCreateItemWithEmptyDescription()
    {
        $bookId = (string) Str::uuid();
        $accountGroupId = (string) Str::uuid();
        $context = [
            'book'          => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
            'accounts'      => [
                'asset'     => ['groups' => []],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'accounttype'   => null,
            'accountcreate' => [
                'grouptitle'  => null,
                'groupid'     => $accountGroupId,
                'itemtitle'   => 'title429',
                'description' => null,
            ],
        ];
        $request = [
            'create'       => 'item',
            'accountgroup' => $accountGroupId,
            'title'        => ' title429 ',
            'description'  => '  ',
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
        $BookKeepingMock->shouldNotReceive('createAccount');
        /** @var \App\Http\Responder\v2\CreateAccountsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateAccountsViewResponder::class);
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
            ->with('create')
            ->andReturn($request['create']);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn($request);

        $controller = new CreateAccountsActionHtml($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $bookId);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTForCreateItemWithEmptyGroup()
    {
        $bookId = (string) Str::uuid();
        $context = [
            'book'          => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
            'accounts'      => [
                'asset'     => ['groups' => []],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'accounttype'   => null,
            'accountcreate' => [
                'grouptitle'  => null,
                'groupid'     => null,
                'itemtitle'   => 'title495',
                'description' => 'description496',
            ],
        ];
        $request = [
            'create'       => 'item',
            'accountgroup' => '0',
            'title'        => ' title495 ',
            'description'  => ' description496 ',
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
        $BookKeepingMock->shouldNotReceive('createAccount');
        /** @var \App\Http\Responder\v2\CreateAccountsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateAccountsViewResponder::class);
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
            ->with('create')
            ->andReturn($request['create']);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn($request);

        $controller = new CreateAccountsActionHtml($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $bookId);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTForCreateItemWithEmptyTitle()
    {
        $bookId = (string) Str::uuid();
        $accountGroupId = (string) Str::uuid();
        $context = [
            'book'          => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
            'accounts'      => [
                'asset'     => ['groups' => []],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'accounttype'   => null,
            'accountcreate' => [
                'grouptitle'  => null,
                'groupid'     => $accountGroupId,
                'itemtitle'   => null,
                'description' => 'description563',
            ],
        ];
        $request = [
            'create'       => 'item',
            'accountgroup' => $accountGroupId,
            'title'        => '  ',
            'description'  => ' description563 ',
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
        $BookKeepingMock->shouldNotReceive('createAccount');
        /** @var \App\Http\Responder\v2\CreateAccountsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateAccountsViewResponder::class);
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
            ->with('create')
            ->andReturn($request['create']);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn($request);

        $controller = new CreateAccountsActionHtml($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $bookId);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTWithoutActionTarget()
    {
        $bookId = (string) Str::uuid();
        $context = [
            'book'          => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
            'accounts'      => [
                'asset'     => ['groups' => []],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'accounttype'   => null,
            'accountcreate' => [
                'grouptitle'  => null,
                'groupid'     => null,
                'itemtitle'   => null,
                'description' => null,
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
        /** @var \App\Http\Responder\v2\CreateAccountsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(CreateAccountsViewResponder::class);
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
            ->with('create')
            ->andReturn(null);

        $controller = new CreateAccountsActionHtml($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock, $bookId);

        $this->assertSame($response_expected, $response_actual);
    }
}
