<?php

namespace Tests\Feature\page\v1\findslips;

use App\Models\Book;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DisplayFindSlipsPageTest extends TestCase
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

    public function test_findslips_page_can_be_diplayed(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/page/v1/findslips');

        $response->assertOk();

        $response->assertSee(__('There is no condition for search.'));
    }

    public function test_findslips_page_does_not_display_because_default_book_is_not_found(): void
    {
        $response = $this->actingAs($this->userWhoDoesNotHaveBook)
            ->get('/page/v1/findslips');

        $response->assertNotFound();
    }
}
