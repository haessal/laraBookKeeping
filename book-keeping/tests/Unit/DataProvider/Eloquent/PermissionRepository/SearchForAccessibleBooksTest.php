<?php

namespace Tests\Unit\DataProvider\Eloquent\PermissionRepository;

use App\DataProvider\Eloquent\PermissionRepository;
use App\Models\Book;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SearchForAccessibleBooksTest extends TestCase
{
    use RefreshDatabase;

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

        $bookList = $this->permission->searchForAccessibleBooks($userId);

        $this->assertFalse(count($bookList) === 0);
        if (! (count($bookList) === 0)) {
            $this->assertSame([
                'book_id',
                'book_name',
                'modifiable',
                'is_owner',
                'is_default',
                'created_at',
            ], array_keys($bookList[0]));
        }
    }
}
