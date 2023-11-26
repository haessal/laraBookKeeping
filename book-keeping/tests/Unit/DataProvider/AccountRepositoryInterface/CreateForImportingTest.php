<?php

namespace Tests\Unit\DataProvider\AccountRepositoryInterface;

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

    public function test_it_takes_one_argument_and_returns_nothing(): void
    {
        $newAccount = [
            'account_id' => (string) Str::uuid(),
            'account_group_id' => (string) Str::uuid(),
            'account_title' => 'title40',
            'description' => 'description41',
            'selectable' => true,
            'bk_uid' => 0,
            'account_bk_code' => 0,
            'display_order' => 0,
            'deleted' => false,
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->account->createForImporting($newAccount);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(true);
    }
}
