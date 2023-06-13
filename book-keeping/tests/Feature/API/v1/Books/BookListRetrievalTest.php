<?php

namespace Tests\Feature\API\v1\Books;

use App\Models\Book;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class BookListRetrievalTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\Book */
    private $book;

    public function setup(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->book = Book::factory()->create([
            'book_name' => $this->faker->word,
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->user->id,
            'readable_book'  => $this->book->book_id,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => true,
        ]);
    }

    public function test_book_list_can_be_retrieved(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/api/v1/books');

        $response->assertOk()
            ->assertJson([
                [
                    'id'           => $this->book->book_id,
                    'name'         => $this->book->book_name,
                    'default'      => true,
                    'own'          => true,
                    'permitted_to' => "ReadWrite",
                    'owner'        => $this->user->name,
                ]
            ]);
    }
}
