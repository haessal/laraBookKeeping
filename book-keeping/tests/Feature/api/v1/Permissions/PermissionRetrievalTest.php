<?php

namespace Tests\Feature\api\v1\Permissions;

use App\Models\Book;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PermissionRetrievalTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\User */
    private $otherUser;

    /** @var \App\Models\Book */
    private $sharedBook;

    /** @var \App\Models\Book */
    private $unavailableBook;

    public function setup(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->sharedBook = Book::factory()->create([
            'book_name' => $this->faker->word,
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->user->id,
            'readable_book'  => $this->sharedBook->book_id,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => false,
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->otherUser->id,
            'readable_book'  => $this->sharedBook->book_id,
            'modifiable'     => false,
            'is_owner'       => false,
            'is_default'     => false,
        ]);
        $this->unavailableBook = Book::factory()->create([
            'book_name' => $this->faker->word,
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->otherUser->id,
            'readable_book'  => $this->unavailableBook->book_id,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => false,
        ]);
    }

    public function test_book_is_not_found(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/api/v1/books/'.$this->unavailableBook->book_id.'/permissions');

        $response->assertNotFound();
    }

    public function test_permission_can_be_retrieved(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/api/v1/books/'.$this->sharedBook->book_id.'/permissions');

        $response->assertOk()
            ->assertJsonFragment([
                [
                    'user'         => $this->user->name,
                    'permitted_to' => 'ReadWrite',
                ],
            ])
            ->assertJsonFragment([
                [
                    'user'         => $this->otherUser->name,
                    'permitted_to' => 'ReadOnly',
                ],
            ]);
    }

    public function test_permission_is_not_retrieved_for_non_owner(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->get('/api/v1/books/'.$this->sharedBook->book_id.'/permissions');

        $response->assertForbidden();
    }

    public function test_permission_is_not_retrieved_with_invalid_path_parameter_for_book_id(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/api/v1/books/0/permissions');

        $response->assertBadRequest();
    }
}
