<?php

namespace Tests\Unit\DataProvider\PermissionRepositoryInterface;

use App\DataProvider\Eloquent\PermissionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class FindBookTest extends TestCase
{
    use RefreshDatabase;

    protected $permission;

    public function setUp(): void
    {
        parent::setUp();
        $this->permission = new PermissionRepository();
    }

    public function test_it_takes_two_arguments_and_returns_an_array_or_null(): void
    {
        $userId = 60;
        $bookId = (string) Str::uuid();

        $book = $this->permission->findBook($userId, $bookId);

        if (is_null($book)) {
            $this->assertNull($book);
        } else {
            $this->assertIsArray($book);
        }
    }
}
