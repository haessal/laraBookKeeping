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
    private $userWhoDoesNotHaveDefaultBook;

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
        $this->userWhoDoesNotHaveDefaultBook = User::factory()->create();
    }

    public function test_default_book_screen_can_be_rendered_with_setting_default_book()
    {
        $response = $this->actingAs($this->user)->get('/settings/default-book');

        $response->assertStatus(200);

        $response->assertSee(__('Remove from the default'));
    }

    public function test_default_book_screen_can_be_rendered_without_setting_default_book()
    {
        $response = $this->actingAs($this->userWhoDoesNotHaveDefaultBook)->get('/settings/default-book');

        $response->assertStatus(200);

        $response->assertSee(__('Select the book to set as the default'));
    }

    public function issueing_personal_access_token_success()
    {
        $response = $this->actingAs($this->user)->post('/settings/default-book');

        $response->assertStatus(200);

        $response->assertSee(__('Make sure to copy your new personal access token now.'));
    }

    public function deleting_personal_access_token_success()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->createToken('personal-access-token');

        $response = $this->actingAs($this->user)->delete('/settings/default-book');

        $response->assertStatus(200);

        $response->assertSee(__('There is no token available.'));
    }
}
