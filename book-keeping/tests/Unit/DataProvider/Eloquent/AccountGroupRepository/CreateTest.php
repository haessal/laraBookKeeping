<?php

namespace Tests\Unit\DataProvider\Eloquent\AccountGroupRepository;

use App\DataProvider\Eloquent\AccountGroupRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    protected $accountGroup;

    public function setUp(): void
    {
        parent::setUp();
        $this->accountGroup = new AccountGroupRepository();
    }

    public function test_one_record_is_created(): void
    {
        $bookId = (string) Str::uuid();
        $accountType = 'asset';
        $title = 'dummy';
        $isCurrent = true;
        $bk_uid = 22;
        $bk_code = 1101;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountGroupId = $this->accountGroup->create($bookId, $accountType, $title, $isCurrent, $bk_uid, $bk_code);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_account_groups', [
            'account_group_id'      => $accountGroupId,
            'book_id'               => $bookId,
            'account_type'          => $accountType,
            'account_group_title'   => $title,
            'bk_uid'                => $bk_uid,
            'account_group_bk_code' => $bk_code,
            'is_current'            => $isCurrent,
        ]);
    }
}
