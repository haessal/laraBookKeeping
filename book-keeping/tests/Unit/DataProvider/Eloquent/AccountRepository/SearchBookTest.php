<?php

namespace Tests\Unit\DataProvider\Eloquent\AccountRepository;

use App\DataProvider\Eloquent\AccountRepository;
use App\Models\Account;
use App\Models\AccountGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchBookTest extends TestCase
{
    use RefreshDatabase;

    protected $account;

    public function setUp(): void
    {
        parent::setUp();
        $this->account = new AccountRepository();
    }

    public function test_the_returned_array_has_keys_as_account(): void
    {
        $bookId = (string) Str::uuid();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountGroupId = AccountGroup::factory()->create([
            'book_id' => $bookId,
            'account_type' => 'asset',
            'account_group_title' => 'dummy group title',
            'bk_uid' => 32,
            'account_group_bk_code' => 1200,
            'is_current' => true,
        ])->account_group_id;
        $accountId = Account::factory()->create([
            'account_group_id' => $accountGroupId,
            'account_title' => 'dummy title',
            'description' => 'description',
            'selectable' => true,
            'bk_uid' => 32,
            'account_bk_code' => 1201,
        ])->account_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $accountList = $this->account->searchBook($bookId);

        $this->assertFalse(count($accountList) === 0);
        if (! (count($accountList) === 0)) {
            $this->assertSame([
                'account_type',
                'account_group_id',
                'account_group_title',
                'is_current',
                'account_id',
                'account_title',
                'description',
                'selectable',
                'account_bk_code',
                'created_at',
                'account_group_bk_code',
                'account_group_created_at',
            ], array_keys($accountList[0]));
        }
    }
}
