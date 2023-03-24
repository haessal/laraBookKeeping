<?php

namespace Tests\Unit\DataProvider\PermissionRepositoryInterface;

use App\DataProvider\Eloquent\PermissionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FindDefaultBookTest extends TestCase
{
    use RefreshDatabase;

    protected $permission;

    public function setUp(): void
    {
        parent::setUp();
        $this->permission = new PermissionRepository();
    }

    public function test_it_takes_one_argument_and_returns_a_value_of_type_string_or_null(): void
    {
        $userId = 20;

        $bookId = $this->permission->findDefaultBook($userId);

        if (is_null($bookId)) {
            $this->assertTrue(is_null($bookId));
        } else {
            $this->assertTrue(is_string($bookId));
        }
    }
}
