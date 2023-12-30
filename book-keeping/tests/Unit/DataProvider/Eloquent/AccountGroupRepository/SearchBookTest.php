<?php

namespace Tests\Unit\DataProvider\Eloquent\AccountGroupRepository;

use App\DataProvider\Eloquent\AccountGroupRepository;
use App\Models\AccountGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchBookTest extends TestCase
{
    use RefreshDatabase;

    protected $accountGroup;

    public function setUp(): void
    {
        parent::setUp();
        $this->accountGroup = new AccountGroupRepository();
    }

    public function test_the_returned_array_has_keys_as_account_group(): void
    {
        $bookId = (string) Str::uuid();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountGroupId = AccountGroup::factory()->create([
            'book_id' => $bookId,
            'account_type' => 'asset',
            'account_group_title' => 'dummy group title',
            'bk_uid' => 68,
            'account_group_bk_code' => null,
            'is_current' => true,
        ])->account_group_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $accountGroupList = $this->accountGroup->searchBook($bookId);

        $this->assertFalse(count($accountGroupList) === 0);
        if (! (count($accountGroupList) === 0)) {
            $this->assertSame([
                'account_group_id',
                'account_type',
                'account_group_title',
                'is_current',
                'account_group_bk_code',
                'created_at',
            ], array_keys($accountGroupList[0]));
        }
    }
}
