<?php

namespace Tests\Unit\DataProvider\Eloquent\PermissionRepository;

use App\DataProvider\Eloquent\PermissionRepository;
use App\Models\Book;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    public function test_it_returns_the_book_id(): void
    {
        $userId = 21;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $bookId_expected = Book::factory()->create([
            'book_name' => 'book_name21',
        ])->book_id;
        $permissionId = Permission::factory()->create([
            'permitted_user' => $userId,
            'readable_book'  => $bookId_expected,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => true,
        ])->permission_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $bookId_actual = $this->permission->findDefaultBook($userId);

        $this->assertSame($bookId_expected, $bookId_actual);
    }
}
