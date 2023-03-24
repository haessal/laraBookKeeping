<?php

namespace Tests\Unit\DataProvider\Eloquent\PermissionRepository;

use App\DataProvider\Eloquent\PermissionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    protected $permission;

    public function setUp(): void
    {
        parent::setUp();
        $this->permission = new PermissionRepository();
    }

    public function test_one_record_is_created(): void
    {
        $userId = 11;
        $bookId = (string) Str::uuid();
        $modifiable = true;
        $is_owner = false;
        $is_default = false;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $permissionId = $this->permission->create($userId, $bookId, $modifiable, $is_owner, $is_default);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_permissions', [
            'permission_id'  => $permissionId,
            'permitted_user' => $userId,
            'readable_book'  => $bookId,
            'modifiable'     => $modifiable,
            'is_owner'       => $is_owner,
            'is_default'     => $is_default,
        ]);
    }
}
