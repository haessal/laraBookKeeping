<?php

namespace Tests\Feature\page\v1\findslips;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Book;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchForSlipsTest extends TestCase
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
            'book_name' => $this->faker->word(),
        ]);
        Permission::factory()->create([
            'permitted_user' => $this->user->id,
            'readable_book' => $this->book->book_id,
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => true,
        ]);
    }

    public function test_user_can_search_for_slips(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v1/findslips', [
                'KEYWORD' => 'sample',
                'buttons' => ['search' => __('Search')],
            ]);

        $response->assertOk();

        $response->assertSee(__('No items that match the condition.'));
    }

    public function test_user_can_not_find_slips_because_of_invalid_parameter(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/page/v1/findslips', [
                'BEGINNING' => '2024-05-1',
                'buttons' => ['search' => __('Search')],
            ]);

        $response->assertOk();

        $response->assertSee(__('Invalid date format.'));
    }
}
