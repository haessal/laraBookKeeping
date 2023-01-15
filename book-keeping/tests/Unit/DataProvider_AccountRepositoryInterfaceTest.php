<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

abstract class DataProvider_AccountRepositoryInterfaceTest extends TestCase
{
    protected $account;

    /**
     * @test
     */
    public function create_ReturnValueTypeIsString()
    {
        $accountGroupId = (string) Str::uuid();
        $title = 'string';
        $description = 'string';
        $bk_uid = 0;
        $bk_code = 0;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountId = $this->account->create($accountGroupId, $title, $description, $bk_uid, $bk_code);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(is_string($accountId));
    }

    /**
     * @test
     */
    public function create_CalledWithNullForNullable()
    {
        $accountGroupId = (string) Str::uuid();
        $title = 'string';
        $description = 'string';

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountId = $this->account->create($accountGroupId, $title, $description, null, null);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(is_string($accountId));
    }

    /**
     * @test
     */
    public function searchBook_ReturnValueTypeIsArray()
    {
        $bookId = (string) Str::uuid();

        $accountList = $this->account->searchBook($bookId);

        $this->assertTrue(is_array($accountList));
    }

    /**
     * @test
     */
    public function update_CalledWithStringAndArray()
    {
        $accountId = (string) Str::uuid();
        $newData = [];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->account->update($accountId, $newData);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(true);
    }
}
