<?php

namespace Tests\Unit;

use App\Http\Responder\Settings\SettingsViewResponder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class Http_Responder_Settings_SettingsViewResponderTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function navilinks_ReturnArray()
    {
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);

        $responder = new SettingsViewResponder($ResponseMock, $ViewFactoryMock);
        $navilinks = $responder->navilinks();

        $this->assertIsArray($navilinks);
    }
}
