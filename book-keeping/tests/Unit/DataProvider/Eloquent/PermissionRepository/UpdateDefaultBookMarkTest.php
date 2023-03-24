<?php

namespace Tests\Unit\DataProvider\Eloquent\PermissionRepository;

use App\DataProvider\Eloquent\PermissionRepository;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateDefaultBookMarkTest extends TestCase
{
    use RefreshDatabase;

    protected $permission;

    public function setUp(): void
    {
        parent::setUp();
        $this->permission = new PermissionRepository();
    }

    public function test_one_record_is_updated(): void
    {
        $userId = 270;
        $bookId = (string) Str::uuid();
        $modifiable = true;
        $is_owner = true;
        $is_default = true;
        $newMark = false;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $permissionId = Permission::factory()->create([
            'permitted_user' => $userId,
            'readable_book'  => $bookId,
            'modifiable'     => $modifiable,
            'is_owner'       => $is_owner,
            'is_default'     => $is_default,
        ])->permission_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->permission->updateDefaultBookMark($userId, $bookId, $newMark);

        $this->assertDatabaseHas('bk2_0_permissions', [
            'permission_id'  => $permissionId,
            'permitted_user' => $userId,
            'readable_book'  => $bookId,
            'modifiable'     => $modifiable,
            'is_owner'       => $is_owner,
            'is_default'     => $newMark,
        ]);
    }
}
