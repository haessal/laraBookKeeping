<?php

namespace Tests\Feature\page\v2\books\bookId\settings;

use App\Models\Book;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DisplaySettingsPageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\User */
    private $userWhoDoesNotHaveBook;

    /** @var \App\Models\Book */
    private $book;

    public function setup(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
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
        $this->userWhoDoesNotHaveBook = User::factory()->create();
    }

    public function test_settings_page_can_be_displayed(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/page/v2/books/'.$this->book->book_id.'/settings');

        $response->assertOk();
    }

    public function test_settings_page_cannot_be_displayed_because_the_uuid_is_invalid(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/page/v2/books/0/settings');

        $response->assertNotFound();
    }

    public function test_the_user_is_not_allowed_to_display_the_settings_page(): void
    {
        $response = $this->actingAs($this->userWhoDoesNotHaveBook)
            ->get('/page/v2/books/'.$this->book->book_id.'/settings');

        $response->assertNotFound();
    }
}
