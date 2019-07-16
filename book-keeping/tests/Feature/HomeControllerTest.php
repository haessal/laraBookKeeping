<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    /**
     * Get Home page normally.
     *
     * @test
     *
     * @return void
     */
    public function get_normally()
    {
        $response = $this->withoutMiddleware()->get('/home');

        $response->assertStatus(200);
    }
}
