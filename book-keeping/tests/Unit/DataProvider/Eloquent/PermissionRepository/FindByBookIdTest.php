<?php

namespace Tests\Unit\DataProvider\Eloquent\PermissionRepository;

use App\DataProvider\Eloquent\PermissionRepository;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class FindByBookIdTest extends TestCase
{
    use RefreshDatabase;

    protected $permission;

    public function setUp(): void
    {
        parent::setUp();
        $this->permission = new PermissionRepository();
    }

    public function test_the_returned_array_has_keys_as_permission(): void
    {
        $userId = 118;
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

        $permissionList = $this->permission->findByBookId($bookId);

        $this->assertFalse(count($permissionList) === 0);
        if (! (count($permissionList) === 0)) {
            $this->assertSame([
                'permitted_user',
                'modifiable',
                'is_owner',
                'is_default',
            ], array_keys($permissionList[0]));
        }
    }
}
