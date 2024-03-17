<?php

namespace Tests\Unit\DataProvider\Eloquent\AccountRepository;

use App\DataProvider\Eloquent\AccountRepository;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchAccountGropupForExportingTest extends TestCase
{
    use RefreshDatabase;

    protected $account;

    public function setUp(): void
    {
        parent::setUp();
        $this->account = new AccountRepository();
    }

    public function test_the_returned_array_has_keys_as_exported_account(): void
    {
        $accountGroupId = (string) Str::uuid();
        $accountTitle = 'title28';
        $description = 'description29';
        $selectable = true;
        $bk_uid = 31;
        $bk_code = 1132;
        $displayOrder = 1;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Account::factory()->create([
            'account_group_id' => $accountGroupId,
            'account_title' => $accountTitle,
            'description' => $description,
            'selectable' => $selectable,
            'bk_uid' => $bk_uid,
            'account_bk_code' => $bk_code,
            'display_order' => $displayOrder,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $accountList = $this->account->searchAccountGropupForExporting($accountGroupId);

        $this->assertFalse(count($accountList) === 0);
        if (! (count($accountList) === 0)) {
            $this->assertSame([
                'account_id',
                'account_group_id',
                'account_title',
                'description',
                'selectable',
                'bk_uid',
                'account_bk_code',
                'display_order',
                'created_at',
                'updated_at',
                'deleted_at',
            ], array_keys($accountList[0]));
        }
    }

    public function test_the_returned_array_has_keys_as_exported_account_even_if_it_is_called_with_account_id(): void
    {
        $accountId = (string) Str::uuid();
        $accountGroupId = (string) Str::uuid();
        $accountTitle = 'title71';
        $description = 'description72';
        $selectable = true;
        $bk_uid = 74;
        $bk_code = 1175;
        $displayOrder = 1;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountId = Account::factory()->create([
            'account_group_id' => $accountGroupId,
            'account_title' => $accountTitle,
            'description' => $description,
            'selectable' => $selectable,
            'bk_uid' => $bk_uid,
            'account_bk_code' => $bk_code,
            'display_order' => $displayOrder,
        ])->account_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $accountList = $this->account->searchAccountGropupForExporting($accountGroupId, $accountId);

        $this->assertFalse(count($accountList) === 0);
        if (! (count($accountList) === 0)) {
            $this->assertSame([
                'account_id',
                'account_group_id',
                'account_title',
                'description',
                'selectable',
                'bk_uid',
                'account_bk_code',
                'display_order',
                'created_at',
                'updated_at',
                'deleted_at',
            ], array_keys($accountList[0]));
        }
    }
}
