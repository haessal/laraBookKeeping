<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    /**
     * Get login page normally.
     *
     * @test
     *
     * @return void
     */
    public function showLoginForm_normally()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * login normally.
     *
     * @test
     *
     * @return void
     */
    public function login_normally()
    {
        $response = $this->withoutMiddleware()->post('/login');

        $response->assertStatus(302);
    }
}
