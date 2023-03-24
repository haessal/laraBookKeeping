<?php

namespace Tests\Unit\DataProvider\PermissionRepositoryInterface;

use App\DataProvider\Eloquent\PermissionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FindUserByNameTest extends TestCase
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
        $name = 'User133';

        $user = $this->permission->findUserByName($name);

        if (is_null($user)) {
            $this->assertTrue(is_null($user));
        } else {
            $this->assertTrue(is_array($user));
        }
    }
}
