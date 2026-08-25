<?php

namespace Tests\Unit\Repositories\Eloquent\PermissionRepository;

use App\Models\DataProvider\Eloquent\Permission;
use App\Repositories\Eloquent\PermissionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @var PermissionRepository */
    protected $permission;

    public function setUp(): void
    {
        parent::setUp();
        $this->permission = new PermissionRepository();
    }

    public function test_one_record_is_deleted(): void
    {
        $userId = 55;
        $bookId = (string) Str::uuid();
        $modifiable = true;
        $is_owner = false;
        $is_default = false;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $permissionId = Permission::factory()->create([
            'permitted_user' => $userId,
            'readable_book' => $bookId,
            'modifiable' => $modifiable,
            'is_owner' => $is_owner,
            'is_default' => $is_default,
        ])->permission_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->permission->delete($userId, $bookId);

        $this->assertDatabaseMissing('bk2_0_permissions', [
            'permission_id' => $permissionId,
            'permitted_user' => $userId,
            'readable_book' => $bookId,
            'modifiable' => $modifiable,
            'is_owner' => $is_owner,
            'is_default' => $is_default,
        ]);
    }
}
