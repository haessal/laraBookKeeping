<?php

namespace Tests\Unit\DataProvider\Eloquent\PermissionRepository;

use App\DataProvider\Eloquent\PermissionRepository;
use App\Models\Book;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    public function test_it_returns_the_owner(): void
    {
        $userName_expected = 'book_name82';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $userId = User::factory()->create([
            'name' => $userName_expected,
        ])->id;
        $bookId = Book::factory()->create([
            'book_name' => 'book_name82',
        ])->book_id;
        $permissionId = Permission::factory()->create([
            'permitted_user' => $userId,
            'readable_book' => $bookId,
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => true,
        ])->permission_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $user_actual = $this->permission->findOwnerOfBook($bookId);

        $this->assertSame($userName_expected, $user_actual['name']);
    }
}
