<?php

namespace Tests\Unit\DataProvider\PermissionRepositoryInterface;

use App\DataProvider\Eloquent\PermissionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FindUserTest extends TestCase
{
    use RefreshDatabase;

    protected $permission;

    public function setUp(): void
    {
        parent::setUp();
        $this->permission = new PermissionRepository();
    }

    public function test_it_takes_one_argument_and_returns_an_array_or_null(): void
    {
        $userId = 117;

        $user = $this->permission->findUser($userId);

        if (is_null($user)) {
            $this->assertTrue(is_null($user));
        } else {
            $this->assertTrue(is_array($user));
        }
    }
}
