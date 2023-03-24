<?php

namespace Tests\Unit\DataProvider\Eloquent\PermissionRepository;

use App\DataProvider\Eloquent\PermissionRepository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    public function test_it_returns_the_user(): void
    {
        $userName_expected = 'user_name202';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $userId = User::factory()->create([
            'name' => $userName_expected,
        ])->id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $user_actual = $this->permission->findUser($userId);

        $this->assertSame($userName_expected, $user_actual['name']);
    }
}
