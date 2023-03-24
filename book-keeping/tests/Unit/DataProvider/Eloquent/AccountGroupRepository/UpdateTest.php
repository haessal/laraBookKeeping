<?php

namespace Tests\Unit\DataProvider\Eloquent\AccountGroupRepository;

use App\DataProvider\Eloquent\AccountGroupRepository;
use App\Models\AccountGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateTest extends TestCase
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
        $title = 'title63';
        $isCurrent = true;
        $title_updated = 'title65';
        $isCurrent_updated = false;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountGroupId = AccountGroup::factory()->create([
            'book_id'               => $bookId,
            'account_type'          => $accountType,
            'account_group_title'   => $title,
            'is_current'            => $isCurrent,
            'bk_uid'                => null,
            'account_group_bk_code' => null,
        ])->account_group_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->accountGroup->update($accountGroupId, [
            'title'      => $title_updated,
            'is_current' => $isCurrent_updated,
        ]);

        $this->assertDatabaseHas('bk2_0_account_groups', [
            'account_group_id'      => $accountGroupId,
            'book_id'               => $bookId,
            'account_type'          => 'asset',
            'account_group_title'   => $title_updated,
            'is_current'            => $isCurrent_updated,
            'bk_uid'                => null,
            'account_group_bk_code' => null,
        ]);
    }
}
