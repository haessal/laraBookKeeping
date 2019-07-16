<?php

namespace Tests\Feature;

use Tests\TestCase;

class HandleErrorStatusCodeTest extends TestCase
{
    /**
     * Handle 404 Not Found.
     *
     * @test
     *
     * @return void
     */
    public function handle_Not_Found()
    {
        $response = $this->get('/notfound');

        $response->assertStatus(404);
    }

    /**
     * Handle 405 Method Not Allowed.
     *
     * @test
     *
     * @return void
     */
    public function handle_Method_Not_Allowed()
    {
        $response = $this->post('/');

        $response->assertStatus(405);
    }
}
