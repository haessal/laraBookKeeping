<?php

namespace Tests\Unit\Repositories\Eloquent\PermissionRepository;

use App\Models\DataProvider\Eloquent\Book;
use App\Models\DataProvider\Eloquent\Permission;
use App\Repositories\Eloquent\PermissionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FindBookTest extends TestCase
{
    use RefreshDatabase;

    /** @var PermissionRepository */
    protected $permission;

    public function setUp(): void
    {
        parent::setUp();
        $this->permission = new PermissionRepository();
    }

    public function test_the_returned_array_has_keys_as_book(): void
    {
        $userId = 31;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $bookId = Book::factory()->create([
            'book_name' => 'book_name31',
        ])->book_id;
        $permissionId = Permission::factory()->create([
            'permitted_user' => $userId,
            'readable_book' => $bookId,
            'modifiable' => true,
            'is_owner' => true,
            'is_default' => true,
        ])->permission_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $book = $this->permission->findBook($userId, $bookId);

        $this->assertSame([
            'book_id',
            'book_name',
            'modifiable',
            'is_owner',
            'is_default',
            'created_at',
        ], array_keys($book));
    }
}
