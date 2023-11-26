<?php

namespace Tests\Unit\DataProvider\Eloquent\AccountRepository;

use App\DataProvider\Eloquent\AccountRepository;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateForImportingTest extends TestCase
{
    use RefreshDatabase;

    protected $account;

    public function setUp(): void
    {
        parent::setUp();
        $this->account = new AccountRepository();
    }

    public function test_one_record_is_updated(): void
    {
        $accountGroupId = (string) Str::uuid();
        $accountTitle = 'title28';
        $description = 'description29';
        $selectable = true;
        $bk_uid = 31;
        $bk_code = 1132;
        $displayOrder = 1;
        $deleted = false;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountId = Account::factory()->create([
            'account_group_id' => (string) Str::uuid(),
            'account_title' => 'title38',
            'description' => 'description39',
            'selectable' => false,
            'bk_uid' => 41,
            'account_bk_code' => 42,
            'display_order' => 2,
        ])->account_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $newAccount = [
            'account_id' => $accountId,
            'account_group_id' => $accountGroupId,
            'account_title' => $accountTitle,
            'description' => $description,
            'selectable' => $selectable,
            'bk_uid' => $bk_uid,
            'account_bk_code' => $bk_code,
            'display_order' => $displayOrder,
            'deleted' => $deleted,
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->account->updateForImporting($newAccount);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_accounts', [
            'account_id' => $accountId,
            'account_group_id' => $accountGroupId,
            'account_title' => $accountTitle,
            'description' => $description,
            'selectable' => $selectable,
            'bk_uid' => $bk_uid,
            'account_bk_code' => $bk_code,
            'display_order' => $displayOrder,
            'deleted_at' => null,
        ]);
    }

    public function test_one_record_is_updated_and_then_deleted(): void
    {
        $accountGroupId = (string) Str::uuid();
        $accountTitle = 'title79';
        $description = 'description80';
        $selectable = true;
        $bk_uid = 82;
        $bk_code = 1183;
        $displayOrder = 1;
        $deleted = true;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountId = Account::factory()->create([
            'account_group_id' => (string) Str::uuid(),
            'account_title' => 'title89',
            'description' => 'description90',
            'selectable' => false,
            'bk_uid' => 92,
            'account_bk_code' => 93,
            'display_order' => 2,
        ])->account_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $newAccount = [
            'account_id' => $accountId,
            'account_group_id' => $accountGroupId,
            'account_title' => $accountTitle,
            'description' => $description,
            'selectable' => $selectable,
            'bk_uid' => $bk_uid,
            'account_bk_code' => $bk_code,
            'display_order' => $displayOrder,
            'deleted' => $deleted,
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->account->updateForImporting($newAccount);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertSoftDeleted('bk2_0_accounts', [
            'account_id' => $accountId,
            'account_group_id' => $accountGroupId,
            'account_title' => $accountTitle,
            'description' => $description,
            'selectable' => $selectable,
            'bk_uid' => $bk_uid,
            'account_bk_code' => $bk_code,
            'display_order' => $displayOrder,
        ]);
    }

    public function test_one_record_is_updated_and_then_restored(): void
    {
        $accountGroupId = (string) Str::uuid();
        $accountTitle = 'title129';
        $description = 'description130';
        $selectable = true;
        $bk_uid = 32;
        $bk_code = 1133;
        $displayOrder = 1;
        $deleted = false;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $account = Account::factory()->create([
            'account_group_id' => (string) Str::uuid(),
            'account_title' => 'title139',
            'description' => 'description140',
            'selectable' => false,
            'bk_uid' => 42,
            'account_bk_code' => 43,
            'display_order' => 2,
        ]);
        $accountId = $account->account_id;
        $account->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $newAccount = [
            'account_id' => $accountId,
            'account_group_id' => $accountGroupId,
            'account_title' => $accountTitle,
            'description' => $description,
            'selectable' => $selectable,
            'bk_uid' => $bk_uid,
            'account_bk_code' => $bk_code,
            'display_order' => $displayOrder,
            'deleted' => $deleted,
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->account->updateForImporting($newAccount);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_accounts', [
            'account_id' => $accountId,
            'account_group_id' => $accountGroupId,
            'account_title' => $accountTitle,
            'description' => $description,
            'selectable' => $selectable,
            'bk_uid' => $bk_uid,
            'account_bk_code' => $bk_code,
            'display_order' => $displayOrder,
            'deleted_at' => null,
        ]);
    }

    public function test_one_record_is_updated_and_still_in_the_trash(): void
    {
        $accountGroupId = (string) Str::uuid();
        $accountTitle = 'title178';
        $description = 'description179';
        $selectable = true;
        $bk_uid = 81;
        $bk_code = 1182;
        $displayOrder = 1;
        $deleted = true;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $account = Account::factory()->create([
            'account_group_id' => (string) Str::uuid(),
            'account_title' => 'title88',
            'description' => 'description89',
            'selectable' => false,
            'bk_uid' => 91,
            'account_bk_code' => 92,
            'display_order' => 2,
        ]);
        $accountId = $account->account_id;
        $account->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $newAccount = [
            'account_id' => $accountId,
            'account_group_id' => $accountGroupId,
            'account_title' => $accountTitle,
            'description' => $description,
            'selectable' => $selectable,
            'bk_uid' => $bk_uid,
            'account_bk_code' => $bk_code,
            'display_order' => $displayOrder,
            'deleted' => $deleted,
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->account->updateForImporting($newAccount);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertSoftDeleted('bk2_0_accounts', [
            'account_id' => $accountId,
            'account_group_id' => $accountGroupId,
            'account_title' => $accountTitle,
            'description' => $description,
            'selectable' => $selectable,
            'bk_uid' => $bk_uid,
            'account_bk_code' => $bk_code,
            'display_order' => $displayOrder,
        ]);
    }
}
