<?php

namespace Tests\Unit\DataProvider\AccountGroupRepositoryInterface;

use App\DataProvider\Eloquent\AccountGroupRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateForImportingTest extends TestCase
{
    use RefreshDatabase;

    protected $accountGroup;

    public function setUp(): void
    {
        parent::setUp();
        $this->accountGroup = new AccountGroupRepository();
    }

    public function test_it_takes_one_argument_and_returns_nothing(): void
    {
        $newAccountGroup = [
            'account_group_id' => (string) Str::uuid(),
            'book_id' => (string) Str::uuid(),
            'account_type' => 'asset',
            'account_group_title' => 'group_title',
            'bk_uid' => 0,
            'account_group_bk_code' => 0,
            'is_current' => true,
            'display_order' => 0,
            'deleted' => false,
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->accountGroup->updateForImporting($newAccountGroup);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(true);
    }
}
