<?php

namespace Tests\Unit;

use App\Http\Responder\v2\UpdateAccountsItemViewResponder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Responder_v2_UpdateAccountsItemViewResponderTest extends TestCase
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
        $accountsId = (string) Str::uuid();
        $accountsGroupId = (string) Str::uuid();
        $context = [
            'accounts'       => [
                'asset'     => ['groups' => []],
                'liability' => ['groups' => []],
                'expense'   => ['groups' => []],
                'revenue'   => ['groups' => []],
            ],
            'accounttypekey' => 'asset',
            'book'           => ['id' => $bookId, 'owner' => 'owner1', 'name' => 'book_name'],
            'accountsitem'   => [
                'id'                   => $accountsId,
                'type'                 => __('Assets'),
                'groupid'              => $accountsGroupId,
                'title'                => 'title40',
                'description'          => 'description41',
                'attribute_selectable' => 'checked',
                'bk_code'              => 1101,
            ],
        ];
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        $ResponseMock->shouldReceive('setContent')->once();
        $ResponseMock->shouldReceive('setStatusCode')->once();
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);
        $ViewFactoryMock->shouldReceive('make')->once();

        $responder = new UpdateAccountsItemViewResponder($ResponseMock, $ViewFactoryMock);
        $response = $responder->response($context);

        $this->assertTrue(true);
    }
}
