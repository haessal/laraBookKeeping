<?php

namespace Tests\Unit\Repositories\AccountRepositoryInterface;

use App\Repositories\Eloquent\AccountRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchAccountGropupForExportingTest extends TestCase
{
    use RefreshDatabase;

    /** @var AccountRepository */
    protected $account;

    public function setUp(): void
    {
        parent::setUp();
        $this->account = new AccountRepository();
    }

    public function test_it_takes_one_argument_and_returns_an_array(): void
    {
        $accountGroupId = (string) Str::uuid();

        $accountList = $this->account->searchAccountGropupForExporting($accountGroupId);

        $this->assertTrue(is_array($accountList));
    }

    public function test_it_takes_two_argument_and_returns_an_array(): void
    {
        $accountGroupId = (string) Str::uuid();
        $accountId = (string) Str::uuid();

        $accountList = $this->account->searchAccountGropupForExporting($accountGroupId, $accountId);

        $this->assertTrue(is_array($accountList));
    }
}
