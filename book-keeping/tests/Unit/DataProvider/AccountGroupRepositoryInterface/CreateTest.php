<?php

namespace Tests\Unit\DataProvider\AccountGroupRepositoryInterface;

use App\DataProvider\Eloquent\AccountGroupRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    protected $accountGroup;

    public function setUp(): void
    {
        parent::setUp();
        $this->accountGroup = new AccountGroupRepository();
    }

    public function test_it_takes_four_arguments_and_returns_a_value_of_type_string(): void
    {
        $bookId = (string) Str::uuid();
        $accountType = 'asset';
        $title = 'string';
        $isCurrent = true;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountGroupId = $this->accountGroup->create($bookId, $accountType, $title, $isCurrent, null, null);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(is_string($accountGroupId));
    }

    public function test_it_takes_six_arguments_and_returns_a_value_of_type_string(): void
    {
        $bookId = (string) Str::uuid();
        $accountType = 'asset';
        $title = 'string';
        $isCurrent = true;
        $bk_uid = 0;
        $bk_code = 0;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountGroupId = $this->accountGroup->create($bookId, $accountType, $title, $isCurrent, $bk_uid, $bk_code);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(is_string($accountGroupId));
    }
}
