<?php

namespace Tests\Unit\DataProvider\Eloquent\AccountGroupRepository;

use App\DataProvider\Eloquent\AccountGroupRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateForImportingTest extends TestCase
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
        $accountGroupId = (string) Str::uuid();
        $bookId = (string) Str::uuid();
        $accountType = 'asset';
        $accountGroupTitle = 'title28';
        $bk_uid = 22;
        $bk_code = 1101;
        $isCurrent = true;
        $displayOrder = 1;
        $deleted = false;
        $newAccountGroup = [
            'account_group_id' => $accountGroupId,
            'book_id' => $bookId,
            'account_type' => $accountType,
            'account_group_title' => $accountGroupTitle,
            'bk_uid' => $bk_uid,
            'account_group_bk_code' => $bk_code,
            'is_current' => $isCurrent,
            'display_order' => $displayOrder,
            'deleted' => $deleted,
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->accountGroup->createForImporting($newAccountGroup);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_account_groups', [
            'account_group_id' => $accountGroupId,
            'book_id' => $bookId,
            'account_type' => $accountType,
            'account_group_title' => $accountGroupTitle,
            'bk_uid' => $bk_uid,
            'account_group_bk_code' => $bk_code,
            'is_current' => $isCurrent,
            'display_order' => $displayOrder,
            'deleted_at' => null,
        ]);
    }

    public function test_one_record_is_created_and_then_deleted(): void
    {
        $accountGroupId = (string) Str::uuid();
        $bookId = (string) Str::uuid();
        $accountType = 'asset';
        $accountGroupTitle = 'title68';
        $bk_uid = 66;
        $bk_code = 1107;
        $isCurrent = true;
        $displayOrder = 1;
        $deleted = true;
        $newAccountGroup = [
            'account_group_id' => $accountGroupId,
            'book_id' => $bookId,
            'account_type' => $accountType,
            'account_group_title' => $accountGroupTitle,
            'bk_uid' => $bk_uid,
            'account_group_bk_code' => $bk_code,
            'is_current' => $isCurrent,
            'display_order' => $displayOrder,
            'deleted' => $deleted,
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->accountGroup->createForImporting($newAccountGroup);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertSoftDeleted('bk2_0_account_groups', [
            'account_group_id' => $accountGroupId,
            'book_id' => $bookId,
            'account_type' => $accountType,
            'account_group_title' => $accountGroupTitle,
            'bk_uid' => $bk_uid,
            'account_group_bk_code' => $bk_code,
            'is_current' => $isCurrent,
            'display_order' => $displayOrder,
        ]);
    }
}
