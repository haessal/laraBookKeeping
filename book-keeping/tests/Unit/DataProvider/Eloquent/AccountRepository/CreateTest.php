<?php

namespace Tests\Unit\DataProvider\Eloquent\AccountRepository;

use App\DataProvider\Eloquent\AccountRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateTest extends TestCase
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
}
