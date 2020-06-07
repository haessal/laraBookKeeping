<?php

namespace Tests\Unit;

use App\Http\Responder\ShowDashboardViewResponder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Responder_ShowDashboardViewResponderTest extends TestCase
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
        $context['books'] = [
            ['id' => (string) Str::uuid(), 'owner' => 'owner', 'name' => 'name']
        ];
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        $ResponseMock->shouldReceive('setContent')->once();
        $ResponseMock->shouldReceive('setStatusCode')->once();
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);
        $ViewFactoryMock->shouldReceive('make')->once();

        $responder = new ShowDashboardViewResponder($ResponseMock, $ViewFactoryMock);
        $response = $responder->response($context);

        $this->assertTrue(true);
    }
}
