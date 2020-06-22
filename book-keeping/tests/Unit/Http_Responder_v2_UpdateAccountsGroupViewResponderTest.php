<?php

namespace Tests\Unit;

use App\Http\Responder\v2\UpdateAccountsGroupViewResponder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Responder_v2_UpdateAccountsGroupViewResponderTest extends TestCase
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
        $accountsGroupId = (string) Str::uuid();
        $context = [
            'accounts'      => [
                'asset'     => ['groups' => []],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'book'          => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
            'accountsgroup' => [
                'id'                => $accountsGroupId,
                'type'              => __('Assets'),
                'title'             => 'title36',
                'attribute_current' => true,
                'bk_code'           => 1100,
            ],
        ];
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        $ResponseMock->shouldReceive('setContent')->once();
        $ResponseMock->shouldReceive('setStatusCode')->once();
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);
        $ViewFactoryMock->shouldReceive('make')->once();

        $responder = new UpdateAccountsGroupViewResponder($ResponseMock, $ViewFactoryMock);
        $response = $responder->response($context);

        $this->assertTrue(true);
    }
}
