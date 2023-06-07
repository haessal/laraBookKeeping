<?php

namespace Tests\Feature\API\v1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class BooksTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    public function setup(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/api/v1/books', ['name' => 'Sally']);

        $response->assertStatus(JsonResponse::HTTP_CREATED);
    }

    public function test_example_fail(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/api/v1/books');

        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);
    }
}
