<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

abstract class DataProvider_AccountGroupRepositoryInterfaceTest extends TestCase
{
    protected $accountGroup;

    /**
     * @test
     */
    public function create_ReturnValueTypeIsString()
    {
        $bookId = (string) Str::uuid();
        $accountType = 'asset';
        $title = 'string';
        $isCurrent = true;
        $bk_uid = 0;
        $bk_code = 0;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountGroupId = $this->accountGroup->create($bookId, $accountType, $title, $isCurrent, $bk_uid, $bk_code);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(is_string($accountGroupId));
    }

    /**
     * @test
     */
    public function create_CalledWithNullForNullable()
    {
        $bookId = (string) Str::uuid();
        $accountType = 'asset';
        $title = 'string';
        $isCurrent = true;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountGroupId = $this->accountGroup->create($bookId, $accountType, $title, $isCurrent, null, null);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(is_string($accountGroupId));
    }

    /**
     * @test
     */
    public function searchBook_ReturnValueTypeIsArray()
    {
        $bookId = (string) Str::uuid();

        $accountGroupList = $this->accountGroup->searchBook($bookId);

        $this->assertTrue(is_array($accountGroupList));
    }

    /**
     * @test
     */
    public function update_CalledWithStringAndArray()
    {
        $accountGroupId = (string) Str::uuid();
        $newData = [];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->accountGroup->update($accountGroupId, $newData);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(true);
    }
}
