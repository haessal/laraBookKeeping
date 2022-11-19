<?php

namespace Tests\Unit;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Models_BookTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_create_new_record()
    {
        $account = Book::factory(1)->create();

        $this->assertEquals(1, count($account));
    }
}
