<?php

namespace Tests\Feature\API\v1\Books;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookCreationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Models\User */
    private $user;

    public function setup(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_book_can_be_created(): void
    {
        $bookName = $this->faker->word;

        $response = $this->actingAs($this->user)
            ->post('/api/v1/books', ['name' => $bookName]);

        $response->assertCreated()
            ->assertJsonStructure(['id', 'name', 'default', 'own', 'permitted_to', 'owner'])
            ->assertJson([
                'name'         => $bookName,
                'default'      => false,
                'own'          => true,
                'permitted_to' => "ReadWrite",
                'owner'        => $this->user->name,
            ]);
        $this->assertDatabaseHas('bk2_0_books', [
            'book_id'   => $response['id'],
            'book_name' => $bookName,
        ]);
        $this->assertDatabaseHas('bk2_0_permissions', [
            'permitted_user' => $this->user->id,
            'readable_book'  => $response['id'],
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => false,
        ]);
    }

    public function test_book_is_not_created_with_invalid_parameter(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/api/v1/books');

        $response->assertBadRequest();
    }
}
