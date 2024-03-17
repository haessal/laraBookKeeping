<?php

namespace Tests\Unit\DataProvider\Eloquent\AccountGroupRepository;

use App\DataProvider\Eloquent\AccountGroupRepository;
use App\Models\AccountGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateForImportingTest extends TestCase
{
    use RefreshDatabase;

    protected $accountGroup;

    public function setUp(): void
    {
        parent::setUp();
        $this->accountGroup = new AccountGroupRepository();
    }

    public function test_one_record_is_updated(): void
    {
        $bookId = (string) Str::uuid();
        $accountType = 'asset';
        $accountGroupTitle = 'title28';
        $bk_uid = 29;
        $bk_code = 1130;
        $isCurrent = true;
        $displayOrder = 1;
        $deleted = false;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountGroupId = AccountGroup::factory()->create([
            'book_id' => (string) Str::uuid(),
            'account_type' => 'liability',
            'account_group_title' => 'title38',
            'bk_uid' => 39,
            'account_group_bk_code' => 1140,
            'is_current' => false,
            'display_order' => 2,
        ])->account_group_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
        $this->accountGroup->updateForImporting($newAccountGroup);
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

    public function test_one_record_is_updated_and_then_deleted(): void
    {
        $bookId = (string) Str::uuid();
        $accountType = 'asset';
        $accountGroupTitle = 'title78';
        $bk_uid = 79;
        $bk_code = 1180;
        $isCurrent = true;
        $displayOrder = 1;
        $deleted = true;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountGroupId = AccountGroup::factory()->create([
            'book_id' => (string) Str::uuid(),
            'account_type' => 'liability',
            'account_group_title' => 'title38',
            'bk_uid' => 39,
            'account_group_bk_code' => 1140,
            'is_current' => false,
            'display_order' => 2,
        ])->account_group_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
        $this->accountGroup->updateForImporting($newAccountGroup);
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

    public function test_one_record_is_updated_and_then_restored(): void
    {
        $bookId = (string) Str::uuid();
        $accountType = 'asset';
        $accountGroupTitle = 'title78';
        $bk_uid = 28;
        $bk_code = 1129;
        $isCurrent = true;
        $displayOrder = 1;
        $deleted = false;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountGroup = AccountGroup::factory()->create([
            'book_id' => (string) Str::uuid(),
            'account_type' => 'liability',
            'account_group_title' => 'title38',
            'bk_uid' => 39,
            'account_group_bk_code' => 1140,
            'is_current' => false,
            'display_order' => 2,
        ]);
        $accountGroupId = $accountGroup->account_group_id;
        $accountGroup->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
        $this->accountGroup->updateForImporting($newAccountGroup);
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

    public function test_one_record_is_updated_and_still_in_the_trash(): void
    {
        $bookId = (string) Str::uuid();
        $accountType = 'asset';
        $accountGroupTitle = 'title79';
        $bk_uid = 80;
        $bk_code = 1181;
        $isCurrent = true;
        $displayOrder = 1;
        $deleted = true;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountGroup = AccountGroup::factory()->create([
            'book_id' => (string) Str::uuid(),
            'account_type' => 'liability',
            'account_group_title' => 'title38',
            'bk_uid' => 39,
            'account_group_bk_code' => 1140,
            'is_current' => false,
            'display_order' => 2,
        ]);
        $accountGroupId = $accountGroup->account_group_id;
        $accountGroup->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
        $this->accountGroup->updateForImporting($newAccountGroup);
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
