<?php

namespace Tests\Unit;

use App\Http\Responder\v2\BaseAccountsViewResponder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class Http_Responder_v2_BaseAccountsViewResponderTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function accountsnavilinks_NaviListIsReturned()
    {
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);

        $responder = new BaseAccountsViewResponder($ResponseMock, $ViewFactoryMock);
        $navilinks = $responder->accountsnavilinks();

        $this->assertIsArray($navilinks);
    }

}
