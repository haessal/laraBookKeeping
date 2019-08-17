<?php

namespace Tests\Feature;

use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    /**
     * Get register page normally.
     *
     * @test
     *
     * @return void
     */
    public function showRegistrationForm_normally()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    /**
     * register normally.
     *
     * @test
     *
     * @return void
     */
    public function register_validate_fail()
    {
        $response = $this->withoutMiddleware()->post('/register');

        $response->assertStatus(302);
    }
}
