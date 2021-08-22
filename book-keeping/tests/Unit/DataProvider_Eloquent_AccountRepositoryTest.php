<?php

namespace Tests\Unit;

use App\DataProvider\Eloquent\Account;
use App\DataProvider\Eloquent\AccountGroup;
use App\DataProvider\Eloquent\AccountRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataProvider_Eloquent_AccountRepositoryTest extends DataProvider_AccountRepositoryInterfaceTest
{
    use RefreshDatabase;

    protected $account;

    public function setUp(): void
    {
        parent::setUp();
        $this->account = new AccountRepository();
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
        $accountGroupId = (string) Str::uuid();
        $title = 'title';
        $description = 'description';
        $bk_uid = 22;
        $bk_code = 1101;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountId = $this->account->create($accountGroupId, $title, $description, $bk_uid, $bk_code);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_accounts', [
            'account_id'                   => $accountId,
            'account_group_id'             => $accountGroupId,
            'account_title'                => $title,
            'description'                  => $description,
            'selectable'                   => true,
            'bk_uid'                       => $bk_uid,
            'account_bk_code'              => $bk_code,
        ]);
    }

    /**
     * @test
     */
    public function searchAccount_ReturnedArrayHasKeysAsAccountList()
    {
        $bookId = (string) Str::uuid();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountGroupId = factory(AccountGroup::class)->create([
            'book_id'               => $bookId,
            'account_type'          => 'asset',
            'account_group_title'   => 'dummy group title',
            'bk_uid'                => 32,
            'account_group_bk_code' => 1200,
            'is_current'            => true,
        ])->account_group_id;
        $accountId = factory(Account::class)->create([
            'account_group_id'       => $accountGroupId,
            'account_title'          => 'dummy title',
            'description'            => 'description',
            'selectable'             => true,
            'bk_uid'                 => 32,
            'account_bk_code'        => 1201,
        ])->account_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $accountList = $this->account->searchAccount($bookId);

        $this->assertFalse(count($accountList) === 0);
        if (!(count($accountList) === 0)) {
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

    /**
     * @test
     */
    public function update_OneRecordIsUpdated()
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
        $accountGroupId_updated = factory(AccountGroup::class)->create([
            'book_id'               => $bookId,
            'account_type'          => 'asset',
            'account_group_title'   => 'group title',
            'bk_uid'                => null,
            'account_group_bk_code' => null,
            'is_current'            => true,
        ])->account_group_id;
        $accountId = factory(Account::class)->create([
            'account_group_id' => $accountGroupId,
            'account_title'    => $title,
            'description'      => $description,
            'selectable'       => $selectable,
            'bk_uid'           => null,
            'account_bk_code'  => null,
        ])->account_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->account->update($accountId, [
            'group'       => $accountGroupId_updated,
            'title'       => $title_updated,
            'description' => $description_updated,
            'selectable'  => $selectable_updated,
        ]);

        $this->assertDatabaseHas('bk2_0_accounts', [
            'account_id'       => $accountId,
            'account_group_id' => $accountGroupId_updated,
            'account_title'    => $title_updated,
            'description'      => $description_updated,
            'selectable'       => $selectable_updated,
            'bk_uid'           => null,
            'account_bk_code'  => null,
        ]);
    }
}
