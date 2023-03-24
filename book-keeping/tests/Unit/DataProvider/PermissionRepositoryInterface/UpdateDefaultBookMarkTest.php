<?php

namespace Tests\Unit\DataProvider\PermissionRepositoryInterface;

use App\DataProvider\Eloquent\PermissionRepository;
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

    public function test_it_takes_three_arguments_and_returns_nothing(): void
    {
        $userId = 149;
        $bookId = (string) Str::uuid();
        $isDefault = true;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->permission->updateDefaultBookMark($userId, $bookId, $isDefault);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(true);
    }
}
