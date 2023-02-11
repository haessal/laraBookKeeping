<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

abstract class DataProvider_PermissionRepositoryInterfaceTest extends TestCase
{
    protected $permission;

    /**
     * @test
     */
    public function create_ReturnValueTypeIsString()
    {
        $userId = 10;
        $bookId = (string) Str::uuid();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $permissionId = $this->permission->create($userId, $bookId, true, true, true);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(is_string($permissionId));
    }

    /**
     * @test
     */
    public function delete_CalledWithIntAndString()
    {
        $userId = 10;
        $bookId = (string) Str::uuid();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->permission->delete($userId, $bookId);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function findBook_ReturnValueTypeIsArrayOrNull()
    {
        $userId = 60;
        $bookId = (string) Str::uuid();

        $book = $this->permission->findBook($userId, $bookId);

        if (is_null($book)) {
            $this->assertNull($book);
        } else {
            $this->assertIsArray($book);
        }
    }

    /**
     * @test
     */
    public function findByBookId_ReturnValueTypeIsArray()
    {
        $bookId = (string) Str::uuid();

        $list = $this->permission->findByBookId($bookId);

        $this->assertTrue(is_array($list));
    }

    /**
     * @test
     */
    public function findDefaultBook_ReturnValueTypeIsStringOrNull()
    {
        $userId = 20;

        $bookId = $this->permission->findDefaultBook($userId);

        if (is_null($bookId)) {
            $this->assertTrue(is_null($bookId));
        } else {
            $this->assertTrue(is_string($bookId));
        }
    }

    /**
     * @test
     */
    public function findOwnerOfBook_ReturnValueTypeIsIntOrNull()
    {
        $bookId = (string) Str::uuid();

        $userId = $this->permission->findOwnerOfBook($bookId);

        if (is_null($userId)) {
            $this->assertTrue(is_null($userId));
        } else {
            $this->assertTrue(is_int($userId));
        }
    }

    /**
     * @test
     */
    public function findUser_ReturnValueTypeIsArrayOrNull()
    {
        $userId = 117;

        $user = $this->permission->findUser($userId);

        if (is_null($user)) {
            $this->assertTrue(is_null($user));
        } else {
            $this->assertTrue(is_array($user));
        }
    }

    /**
     * @test
     */
    public function findUserByName_ReturnValueTypeIsArrayOrNull()
    {
        $name = 'User133';

        $user = $this->permission->findUserByName($name);

        if (is_null($user)) {
            $this->assertTrue(is_null($user));
        } else {
            $this->assertTrue(is_array($user));
        }
    }

    /**
     * @test
     */
    public function searchForAccessibleBooks_ReturnValueTypeIsArray()
    {
        $userId = 30;

        $bookList = $this->permission->searchForAccessibleBooks($userId);

        $this->assertTrue(is_array($bookList));
    }

    /**
     * @test
     */
    public function updateDefaultBookMark_ReturnVoid()
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
