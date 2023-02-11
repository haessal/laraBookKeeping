<?php

namespace Tests\Unit;

use App\DataProvider\Eloquent\PermissionRepository;
use App\Models\Book;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataProvider_Eloquent_PermissionRepositoryTest extends DataProvider_PermissionRepositoryInterfaceTest
{
    use RefreshDatabase;

    protected $permission;

    public function setUp(): void
    {
        parent::setUp();
        $this->permission = new PermissionRepository();
    }

    /**
     * @test
     */
    public function create_OneRecordIsCreated()
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

    /**
     * @test
     */
    public function delete_OneRecordIsSoftDeleted()
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

    /**
     * @test
     */
    public function findBook_ReturnedArrayHasKeysAsBook()
    {
        $userId = 31;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $bookId = Book::factory()->create([
            'book_name' => 'book_name31',
        ])->book_id;
        $permissionId = Permission::factory()->create([
            'permitted_user' => $userId,
            'readable_book'  => $bookId,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => true,
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

    /**
     * @test
     */
    public function findByBookId_ReturnedArrayHasKeysAsPermissionList()
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

    /**
     * @test
     */
    public function findDefaultBook_ReturnBookIdOfDefaultBook()
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

    /**
     * @test
     */
    public function findOwnerOfBook_ReturnUserIdOfOwner()
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
            'readable_book'  => $bookId,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => true,
        ])->permission_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $user_actual = $this->permission->findOwnerOfBook($bookId);

        $this->assertSame($userName_expected, $user_actual['name']);
    }

    /**
     * @test
     */
    public function findUser_ReturnUser()
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

    /**
     * @test
     */
    public function findUserByName_ReturnUser()
    {
        $userName_expected = 'user_name219';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $userId = User::factory()->create([
            'name' => $userName_expected,
        ])->id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $user_actual = $this->permission->findUserByName($userName_expected);

        $this->assertSame($userName_expected, $user_actual['name']);
    }

    /**
     * @test
     */
    public function searchForAccessibleBooks_ReturnedArrayHasKeysAsBookList()
    {
        $userId = 31;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $bookId = Book::factory()->create([
            'book_name' => 'book_name31',
        ])->book_id;
        $permissionId = Permission::factory()->create([
            'permitted_user' => $userId,
            'readable_book'  => $bookId,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => true,
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

    /**
     * @test
     */
    public function updateDefaultBookMark_OneRecordIsUpdated()
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
