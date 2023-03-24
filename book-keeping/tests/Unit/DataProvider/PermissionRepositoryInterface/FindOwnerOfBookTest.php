<?php

namespace Tests\Unit\DataProvider\PermissionRepositoryInterface;

use App\DataProvider\Eloquent\PermissionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class FindOwnerOfBookTest extends TestCase
{
    use RefreshDatabase;

    protected $permission;

    public function setUp(): void
    {
        parent::setUp();
        $this->permission = new PermissionRepository();
    }

    public function test_it_takes_one_argument_and_returns_a_value_of_type_int_or_null(): void
    {
        $bookId = (string) Str::uuid();

        $userId = $this->permission->findOwnerOfBook($bookId);

        if (is_null($userId)) {
            $this->assertTrue(is_null($userId));
        } else {
            $this->assertTrue(is_int($userId));
        }
    }
}
