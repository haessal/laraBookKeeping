<?php

namespace Tests\Feature\settings;

use App\Models\Book;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateDefaultBookTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\User */
    private $userWhoDoesNotHaveDefaultBook1;

    /** @var \App\Models\User */
    private $userWhoDoesNotHaveDefaultBook2;

    /** @var \App\Models\Book */
    private $book;

    /** @var \App\Models\Book */
    private $bookToBeSetAsDefault;

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
        $this->userWhoDoesNotHaveDefaultBook1 = User::factory()->create();
        $this->bookToBeSetAsDefault = Book::factory()->create([
            'book_name' => $this->faker->word(),
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->userWhoDoesNotHaveDefaultBook1->id,
            'readable_book' => $this->bookToBeSetAsDefault->book_id,
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => false,
        ]);
        $this->userWhoDoesNotHaveDefaultBook2 = User::factory()->create();
    }

    public function test_default_book_screen_can_be_rendered_with_setting_default_book()
    {
        $response = $this->actingAs($this->user)->get('/settings/default-book');

        $response->assertStatus(200);

        $response->assertSee(__('Remove from the default'));
    }

    public function test_default_book_screen_can_be_rendered_without_setting_default_book()
    {
        $response = $this->actingAs($this->userWhoDoesNotHaveDefaultBook1)->get('/settings/default-book');

        $response->assertStatus(200);

        $response->assertSee(__('Select the book to set as the default.'));
    }

    public function test_setting_the_book_as_the_default_book()
    {
        $response = $this->actingAs($this->userWhoDoesNotHaveDefaultBook1)->post('/settings/default-book', [
            'selectedBook' => $this->bookToBeSetAsDefault->book_id,
        ]);

        $response->assertStatus(200);

        $response->assertSee(__('Remove from the default'));
    }

    public function test_removing_the_book_from_the_default_books()
    {
        $response = $this->actingAs($this->user)->delete('/settings/default-book');

        $response->assertStatus(200);

        $response->assertSee(__('Select the book to set as the default.'));
    }

    public function test_removing_the_book_from_the_default_books_but_the_default_book_was_not_set_already()
    {
        $response = $this->actingAs($this->userWhoDoesNotHaveDefaultBook2)->delete('/settings/default-book');

        $response->assertStatus(200);

        $response->assertSee(__('Select the book to set as the default.'));
    }
}
