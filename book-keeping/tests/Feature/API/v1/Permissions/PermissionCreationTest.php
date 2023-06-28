<?php

namespace Tests\Feature\API\v1\Permissions;

use App\Models\Book;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PermissionCreationTest extends TestCase
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
    private $sharedWritableBook;

    /** @var \App\Models\Book */
    private $bookToBeSharedToRead;

    /** @var \App\Models\Book */
    private $bookToBeSharedToReadWrite;

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
            'is_default'     => true,
        ]);
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
        $this->sharedWritableBook = Book::factory()->create([
            'book_name' => $this->faker->word,
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->user->id,
            'readable_book'  => $this->sharedWritableBook->book_id,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => false,
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->otherUser->id,
            'readable_book'  => $this->sharedWritableBook->book_id,
            'modifiable'     => true,
            'is_owner'       => false,
            'is_default'     => false,
        ]);
        $this->bookToBeSharedToRead = Book::factory()->create([
            'book_name' => $this->faker->word,
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->user->id,
            'readable_book'  => $this->bookToBeSharedToRead->book_id,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => true,
        ]);
        $this->bookToBeSharedToReadWrite = Book::factory()->create([
            'book_name' => $this->faker->word,
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->user->id,
            'readable_book'  => $this->bookToBeSharedToReadWrite->book_id,
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

    public function test_book_is_not_found(): void
    {
        $response = $this->actingAs($this->user)
            ->put('/api/v1/books/'.$this->unavailableBook->book_id.'/permissions', [
                'user'         => $this->otherUser->name,
                'permitted_to' => 'ReadWrite',
            ]);

        $response->assertNotFound();
    }

    public function test_permission_is_not_updated_by_non_owner(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->put('/api/v1/books/'.$this->sharedBook->book_id.'/permissions', [
                'user'         => $this->otherUser->name,
                'permitted_to' => 'ReadWrite',
            ]);

        $response->assertForbidden();
    }

    public function test_permission_is_not_updated_with_invalid_path_parameter_for_book_id(): void
    {
        $response = $this->actingAs($this->user)
            ->put('/api/v1/books/0/permissions', [
                'user'         => $this->otherUser->name,
                'permitted_to' => 'ReadWrite',
            ]);

        $response->assertBadRequest();
    }
    
    public function test_permission_is_not_updated_with_invalid_request_body(): void
    {
        $response = $this->actingAs($this->user)
            ->put('/api/v1/books/'.$this->book->book_id.'/permissions');

        $response->assertBadRequest();
    }

    public function test_permission_for_non_owner_can_be_updated_to_read_only(): void
    {
        $response = $this->actingAs($this->user)
            ->put('/api/v1/books/'.$this->bookToBeSharedToRead->book_id.'/permissions', [
                'user'         => $this->otherUser->name,
                'permitted_to' => 'ReadOnly',
            ]);

        $response->assertOk()
            ->assertJsonFragment([
                [
                    'user'         => $this->otherUser->name,
                    'permitted_to' => 'ReadOnly',
                ],
            ]);
        $this->assertDatabaseHas('bk2_0_permissions', [
            'permitted_user' => $this->otherUser->id,
            'readable_book'  => $this->bookToBeSharedToRead->book_id,
            'modifiable'     => false,
            'is_owner'       => false,
            'is_default'     => false,
        ]);    
    }

    public function test_permission_for_non_owner_can_be_updated_to_read_write(): void
    {
        $response = $this->actingAs($this->user)
            ->put('/api/v1/books/'.$this->bookToBeSharedToReadWrite->book_id.'/permissions', [
                'user'         => $this->otherUser->name,
                'permitted_to' => 'ReadWrite',
            ]);

        $response->assertOk()
            ->assertJsonFragment([
                [
                    'user'         => $this->otherUser->name,
                    'permitted_to' => 'ReadWrite',
                ],
            ]);
        $this->assertDatabaseHas('bk2_0_permissions', [
            'permitted_user' => $this->otherUser->id,
            'readable_book'  => $this->bookToBeSharedToReadWrite->book_id,
            'modifiable'     => true,
            'is_owner'       => false,
            'is_default'     => false,
        ]);    
    }

    public function test_permission_for_non_owner_is_already_updated_to_read_only(): void
    {
        $response = $this->actingAs($this->user)
            ->put('/api/v1/books/'.$this->sharedBook->book_id.'/permissions', [
                'user'         => $this->otherUser->name,
                'permitted_to' => 'ReadOnly',
            ]);

        $response->assertOk()
            ->assertJsonFragment([
                [
                    'user'         => $this->otherUser->name,
                    'permitted_to' => 'ReadOnly',
                ],
            ]);
        $this->assertDatabaseHas('bk2_0_permissions', [
            'permitted_user' => $this->otherUser->id,
            'readable_book'  => $this->sharedBook->book_id,
            'modifiable'     => false,
            'is_owner'       => false,
            'is_default'     => false,
        ]);    
    }

    public function test_permission_for_non_owner_is_already_updated_to_read_write(): void
    {
        $response = $this->actingAs($this->user)
            ->put('/api/v1/books/'.$this->sharedWritableBook->book_id.'/permissions', [
                'user'         => $this->otherUser->name,
                'permitted_to' => 'ReadWrite',
            ]);

        $response->assertOk()
            ->assertJsonFragment([
                [
                    'user'         => $this->otherUser->name,
                    'permitted_to' => 'ReadWrite',
                ],
            ]);
        $this->assertDatabaseHas('bk2_0_permissions', [
            'permitted_user' => $this->otherUser->id,
            'readable_book'  => $this->sharedWritableBook->book_id,
            'modifiable'     => true,
            'is_owner'       => false,
            'is_default'     => false,
        ]);    
    }

    public function test_permission_for_non_owner_is_not_updated_to_read_only_with_bad_condition(): void
    {
        $response = $this->actingAs($this->user)
            ->put('/api/v1/books/'.$this->sharedWritableBook->book_id.'/permissions', [
                'user'         => $this->otherUser->name,
                'permitted_to' => 'ReadOnly',
            ]);

        $response->assertUnprocessable();
        $this->assertDatabaseHas('bk2_0_permissions', [
            'permitted_user' => $this->otherUser->id,
            'readable_book'  => $this->sharedWritableBook->book_id,
            'modifiable'     => true,
            'is_owner'       => false,
            'is_default'     => false,
        ]);    
    }

    public function test_permission_for_non_owner_is_not_updated_to_read_write_with_bad_condition(): void
    {
        $response = $this->actingAs($this->user)
            ->put('/api/v1/books/'.$this->sharedBook->book_id.'/permissions', [
                'user'         => $this->otherUser->name,
                'permitted_to' => 'ReadWrite',
            ]);

        $response->assertUnprocessable();
        $this->assertDatabaseHas('bk2_0_permissions', [
            'permitted_user' => $this->otherUser->id,
            'readable_book'  => $this->sharedBook->book_id,
            'modifiable'     => false,
            'is_owner'       => false,
            'is_default'     => false,
        ]);    
    }

    public function test_permission_for_owner_is_already_updated_to_read_write(): void
    {
        $response = $this->actingAs($this->user)
            ->put('/api/v1/books/'.$this->book->book_id.'/permissions', [
                'user'         => $this->user->name,
                'permitted_to' => 'ReadWrite',
            ]);

        $response->assertOk()
            ->assertJsonFragment([
                [
                    'user'         => $this->user->name,
                    'permitted_to' => 'ReadWrite',
                ],
            ]);
        $this->assertDatabaseHas('bk2_0_permissions', [
            'permitted_user' => $this->user->id,
            'readable_book'  => $this->book->book_id,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => true,
        ]);    
    }

    public function test_permission_for_owner_is_not_updated_to_read_only_with_bad_condition(): void
    {
        $response = $this->actingAs($this->user)
            ->put('/api/v1/books/'.$this->book->book_id.'/permissions', [
                'user'         => $this->user->name,
                'permitted_to' => 'ReadOnly',
            ]);

        $response->assertUnprocessable();
        $this->assertDatabaseHas('bk2_0_permissions', [
            'permitted_user' => $this->user->id,
            'readable_book'  => $this->book->book_id,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => true,
        ]);    
    }
}
