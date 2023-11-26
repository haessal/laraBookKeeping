<?php

namespace Tests\Unit\DataProvider\Eloquent\AccountRepository;

use App\DataProvider\Eloquent\AccountRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateForImportingTest extends TestCase
{
    use RefreshDatabase;

    protected $account;

    public function setUp(): void
    {
        parent::setUp();
        $this->account = new AccountRepository();
    }

    public function test_one_record_is_created(): void
    {
        $accountId = (string) Str::uuid();
        $accountGroupId = (string) Str::uuid();
        $accountTitle = 'title27';
        $description = 'description28';
        $selectable = true;
        $bk_uid = 22;
        $bk_code = 1101;
        $displayOrder = 1;
        $deleted = false;
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
        $this->account->createForImporting($newAccount);
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

    public function test_one_record_is_created_and_then_deleted(): void
    {
        $accountId = (string) Str::uuid();
        $accountGroupId = (string) Str::uuid();
        $accountTitle = 'title27';
        $description = 'description28';
        $selectable = true;
        $bk_uid = 70;
        $bk_code = 1171;
        $displayOrder = 1;
        $deleted = true;
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
        $this->account->createForImporting($newAccount);
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
