<?php

namespace Tests\Feature\Models;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_basic_model_can_create_a_new_record(): void
    {
        $account = Book::factory(1)->create();

        $this->assertEquals(1, count($account));
    }
}
