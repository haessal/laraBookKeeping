<?php

namespace Tests\Unit;

use App\Http\Responder\v2\CreateAccountsViewResponder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Responder_v2_CreateAccountsViewResponderTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function response_ReturnResponse()
    {
        $bookId = (string) Str::uuid();
        $context = [
            'accounts'      => [
                'asset'     => ['groups' => []],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'accounttype'   => 'liability',
            'book'          => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
            'accountcreate' => [
                'grouptitle'  => null,
                'groupid'     => null,
                'itemtitle'   => null,
                'description' => null,
            ],
        ];

        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        $ResponseMock->shouldReceive('setContent')->once();
        $ResponseMock->shouldReceive('setStatusCode')->once();
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);
        $ViewFactoryMock->shouldReceive('make')->once();

        $responder = new CreateAccountsViewResponder($ResponseMock, $ViewFactoryMock);
        $response = $responder->response($context);

        $this->assertTrue(true);
    }
}
