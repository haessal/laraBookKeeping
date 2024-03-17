<?php

namespace Tests\Unit\DataProvider\Eloquent\AccountGroupRepository;

use App\DataProvider\Eloquent\AccountGroupRepository;
use App\Models\AccountGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchBookForExportingTest extends TestCase
{
    use RefreshDatabase;

    protected $accountGroup;

    public function setUp(): void
    {
        parent::setUp();
        $this->accountGroup = new AccountGroupRepository();
    }

    public function test_the_returned_array_has_keys_as_exported_account_group(): void
    {
        $bookId = (string) Str::uuid();
        $accountType = 'asset';
        $accountGroupTitle = 'title29';
        $bk_uid = 30;
        $bk_code = 1103;
        $isCurrent = true;
        $displayOrder = 1;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        AccountGroup::factory()->create([
            'book_id' => $bookId,
            'account_type' => $accountType,
            'account_group_title' => $accountGroupTitle,
            'bk_uid' => $bk_uid,
            'account_group_bk_code' => $bk_code,
            'is_current' => $isCurrent,
            'display_order' => $displayOrder,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $accountGroupList = $this->accountGroup->searchBookForExporting($bookId);

        $this->assertFalse(count($accountGroupList) === 0);
        if (! (count($accountGroupList) === 0)) {
            $this->assertSame([
                'account_group_id',
                'book_id',
                'account_type',
                'account_group_title',
                'bk_uid',
                'account_group_bk_code',
                'is_current',
                'display_order',
                'created_at',
                'updated_at',
                'deleted_at',
            ], array_keys($accountGroupList[0]));
        }
    }

    public function test_the_returned_array_has_keys_as_exported_account_group_even_if_it_is_called_with_account_group_id(): void
    {
        $bookId = (string) Str::uuid();
        $accountType = 'asset';
        $accountGroupTitle = 'title29';
        $bk_uid = 30;
        $bk_code = 1103;
        $isCurrent = true;
        $displayOrder = 1;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountGroupId = AccountGroup::factory()->create([
            'book_id' => $bookId,
            'account_type' => $accountType,
            'account_group_title' => $accountGroupTitle,
            'bk_uid' => $bk_uid,
            'account_group_bk_code' => $bk_code,
            'is_current' => $isCurrent,
            'display_order' => $displayOrder,
        ])->account_group_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $accountGroupList = $this->accountGroup->searchBookForExporting($bookId, $accountGroupId);

        $this->assertFalse(count($accountGroupList) === 0);
        if (! (count($accountGroupList) === 0)) {
            $this->assertSame([
                'account_group_id',
                'book_id',
                'account_type',
                'account_group_title',
                'bk_uid',
                'account_group_bk_code',
                'is_current',
                'display_order',
                'created_at',
                'updated_at',
                'deleted_at',
            ], array_keys($accountGroupList[0]));
        }
    }
}
