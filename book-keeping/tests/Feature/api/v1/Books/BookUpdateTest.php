<?php

namespace Tests\Feature\api\v1\Books;

use App\Models\Book;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookUpdateTest extends TestCase
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
            'book_name' => $this->faker->word(),
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->user->id,
            'readable_book' => $this->book->book_id,
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => true,
        ]);
        $this->sharedBook = Book::factory()->create([
            'book_name' => $this->faker->word(),
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->otherUser->id,
            'readable_book' => $this->sharedBook->book_id,
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => false,
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->user->id,
            'readable_book' => $this->sharedBook->book_id,
            'modifiable' => false,
            'is_owner' => false,
            'is_default' => false,
        ]);
        $this->unavailableBook = Book::factory()->create([
            'book_name' => $this->faker->word(),
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->otherUser->id,
            'readable_book' => $this->unavailableBook->book_id,
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => true,
        ]);
    }

    public function test_book_can_be_updated(): void
    {
        $newBookName = $this->faker->word();

        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/'.$this->book->book_id, ['name' => $newBookName]);

        $response->assertOk()
            ->assertJson([
                'id' => $this->book->book_id,
                'name' => $newBookName,
                'default' => true,
                'own' => true,
                'permitted_to' => 'ReadWrite',
                'owner' => $this->user->name,
            ]);
        $this->assertDatabaseHas('bk2_0_books', [
            'book_id' => $this->book->book_id,
            'book_name' => $newBookName,
        ]);
    }

    public function test_book_is_not_found(): void
    {
        $oldBookName = $this->unavailableBook->book_name;
        $newBookName = $this->faker->word();

        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/'.$this->unavailableBook->book_id, ['name' => $newBookName]);

        $response->assertNotFound();
        $this->assertDatabaseHas('bk2_0_books', [
            'book_id' => $this->unavailableBook->book_id,
            'book_name' => $oldBookName,
        ]);
    }

    public function test_book_is_not_updated_with_invalid_path_parameter_for_book_id(): void
    {
        $oldBookName = $this->book->book_name;
        $newBookName = $this->faker->word();

        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/0', ['name' => $newBookName]);

        $response->assertBadRequest();
        $this->assertDatabaseHas('bk2_0_books', [
            'book_id' => $this->book->book_id,
            'book_name' => $oldBookName,
        ]);
    }

    public function test_book_is_not_updated_with_invalid_request_body(): void
    {
        $oldBookName = $this->book->book_name;

        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/'.$this->book->book_id);

        $response->assertBadRequest();
        $this->assertDatabaseHas('bk2_0_books', [
            'book_id' => $this->book->book_id,
            'book_name' => $oldBookName,
        ]);
    }

    public function test_book_is_not_updated_without_permission(): void
    {
        $oldBookName = $this->sharedBook->book_name;
        $newBookName = $this->faker->word();

        $response = $this->actingAs($this->user)
            ->patch('/api/v1/books/'.$this->sharedBook->book_id, ['name' => $newBookName]);

        $response->assertForbidden();
        $this->assertDatabaseHas('bk2_0_books', [
            'book_id' => $this->sharedBook->book_id,
            'book_name' => $oldBookName,
        ]);
    }
}
