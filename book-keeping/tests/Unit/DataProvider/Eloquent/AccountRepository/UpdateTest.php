<?php

namespace Tests\Unit\DataProvider\Eloquent\AccountRepository;

use App\DataProvider\Eloquent\AccountRepository;
use App\Models\Account;
use App\Models\AccountGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateTest extends TestCase
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
        $bookId = (string) Str::uuid();
        $accountGroupId = (string) Str::uuid();
        $title = 'title109';
        $description = 'description110';
        $selectable = true;
        //$accountGroupId_updated = (string) Str::uuid();
        $title_updated = 'title113';
        $description_updated = 'description114';
        $selectable_updated = false;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountGroupId_updated = AccountGroup::factory()->create([
            'book_id' => $bookId,
            'account_type' => 'asset',
            'account_group_title' => 'group title',
            'bk_uid' => null,
            'account_group_bk_code' => null,
            'is_current' => true,
        ])->account_group_id;
        $accountId = Account::factory()->create([
            'account_group_id' => $accountGroupId,
            'account_title' => $title,
            'description' => $description,
            'selectable' => $selectable,
            'bk_uid' => null,
            'account_bk_code' => null,
        ])->account_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->account->update($accountId, [
            'group' => $accountGroupId_updated,
            'title' => $title_updated,
            'description' => $description_updated,
            'selectable' => $selectable_updated,
        ]);

        $this->assertDatabaseHas('bk2_0_accounts', [
            'account_id' => $accountId,
            'account_group_id' => $accountGroupId_updated,
            'account_title' => $title_updated,
            'description' => $description_updated,
            'selectable' => $selectable_updated,
            'bk_uid' => null,
            'account_bk_code' => null,
        ]);
    }
}
