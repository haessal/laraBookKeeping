<?php

namespace Tests\Unit\DataProvider\AccountRepositoryInterface;

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

    public function test_it_takes_five_arguments_and_returns_a_value_of_type_string(): void
    {
        $accountGroupId = (string) Str::uuid();
        $title = 'string';
        $description = 'string';
        $bk_uid = 0;
        $bk_code = 0;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountId = $this->account->create($accountGroupId, $title, $description, $bk_uid, $bk_code);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(is_string($accountId));
    }

    public function test_it_takes_three_arguments_and_returns_a_value_of_type_string(): void
    {
        $accountGroupId = (string) Str::uuid();
        $title = 'string';
        $description = 'string';

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountId = $this->account->create($accountGroupId, $title, $description, null, null);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(is_string($accountId));
    }
}
