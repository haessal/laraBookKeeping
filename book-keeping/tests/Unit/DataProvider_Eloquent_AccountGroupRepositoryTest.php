<?php

namespace Tests\Unit;

use App\DataProvider\Eloquent\AccountGroup;
use App\DataProvider\Eloquent\AccountGroupRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataProvider_Eloquent_AccountGroupRepositoryTest extends DataProvider_AccountGroupRepositoryInterfaceTest
{
    use RefreshDatabase;

    protected $accountGroup;

    public function setUp(): void
    {
        parent::setUp();
        $this->accountGroup = new AccountGroupRepository();
    }

    public function tearDown(): void
    {
        Artisan::call('migrate:refresh');
        parent::tearDown();
    }

    /**
     * @test
     */
    public function create_OneRecordIsCreated()
    {
        $bookId = (string) Str::uuid();
        $accountType = 'asset';
        $title = 'dummy';
        $isCurrent = true;
        $bk_uid = 22;
        $bk_code = 1101;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountGroupId = $this->accountGroup->create($bookId, $accountType, $title, $isCurrent, $bk_uid, $bk_code);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_account_groups', [
            'account_group_id'      => $accountGroupId,
            'book_id'               => $bookId,
            'account_type'          => $accountType,
            'account_group_title'   => $title,
            'bk_uid'                => $bk_uid,
            'account_group_bk_code' => $bk_code,
            'is_current'            => $isCurrent,
        ]);
    }

    /**
     * @test
     */
    public function update_OneRecordIsUpdated()
    {
        $bookId = (string) Str::uuid();
        $accountType = 'asset';
        $title = 'title63';
        $isCurrent = true;
        $title_updated = 'title65';
        $isCurrent_updated = false;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountGroupId = factory(AccountGroup::class)->create([
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
