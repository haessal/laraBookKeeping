<?php

namespace Tests\Feature\api\v1\Books;

use App\Models\Book;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DefaultBookCreationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\User */
    private $otherUser;

    /** @var \App\Models\Book */
    private $book;

    /** @var \App\Models\Book */
    private $sharedBook;

    /** @var \App\Models\Book */
    private $unavailableBook;

    public function setup(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->book = Book::factory()->create([
            'book_name' => $this->faker->word,
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->user->id,
            'readable_book'  => $this->book->book_id,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => false,
        ]);
        $this->sharedBook = Book::factory()->create([
            'book_name' => $this->faker->word,
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->user->id,
            'readable_book'  => $this->sharedBook->book_id,
            'modifiable'     => false,
            'is_owner'       => false,
            'is_default'     => false,
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->otherUser->id,
            'readable_book'  => $this->sharedBook->book_id,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => true,
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

    public function test_book_can_be_updated(): void
    {
        $response = $this->actingAs($this->user)
            ->put('/api/v1/books/'.$this->book->book_id.'/default');

        $response->assertOk()
            ->assertJson([
                'id'           => $this->book->book_id,
                'name'         => $this->book->book_name,
                'default'      => true,
                'own'          => true,
                'permitted_to' => 'ReadWrite',
                'owner'        => $this->user->name,
            ]);
        $this->assertDatabaseHas('bk2_0_permissions', [
            'permitted_user' => $this->user->id,
            'readable_book'  => $this->book->book_id,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => true,
        ]);
    }

    public function test_book_is_already_updated(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->put('/api/v1/books/'.$this->sharedBook->book_id.'/default');

        $response->assertOk()
            ->assertJson([
                'id'           => $this->sharedBook->book_id,
                'name'         => $this->sharedBook->book_name,
                'default'      => true,
                'own'          => true,
                'permitted_to' => 'ReadWrite',
                'owner'        => $this->otherUser->name,
            ]);
        $this->assertDatabaseHas('bk2_0_permissions', [
            'permitted_user' => $this->otherUser->id,
            'readable_book'  => $this->sharedBook->book_id,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => true,
        ]);
    }

    public function test_book_is_not_found(): void
    {
        $response = $this->actingAs($this->user)
            ->put('/api/v1/books/'.$this->unavailableBook->book_id.'/default');

        $response->assertNotFound();
    }

    public function test_book_is_not_updated_with_invalid_path_parameter_for_book_id(): void
    {
        $response = $this->actingAs($this->user)
            ->put('/api/v1/books/0/default');

        $response->assertBadRequest();
    }

    public function test_book_is_not_updated_with_bad_condition(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->put('/api/v1/books/'.$this->unavailableBook->book_id.'/default');

        $response->assertUnprocessable();
        $this->assertDatabaseHas('bk2_0_permissions', [
            'permitted_user' => $this->otherUser->id,
            'readable_book'  => $this->unavailableBook->book_id,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => false,
        ]);
    }

    public function test_book_is_not_updated_without_permission(): void
    {
        $response = $this->actingAs($this->user)
            ->put('/api/v1/books/'.$this->sharedBook->book_id.'/default');

        $response->assertForbidden();
    }
}
