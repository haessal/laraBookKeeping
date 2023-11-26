<?php

namespace Tests\Unit\DataProvider\AccountGroupRepositoryInterface;

use App\DataProvider\Eloquent\AccountGroupRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_it_takes_one_argument_and_returns_an_array(): void
    {
        $bookId = (string) Str::uuid();

        $accountGroupList = $this->accountGroup->searchBookForExporting($bookId);

        $this->assertTrue(is_array($accountGroupList));
    }

    public function test_it_takes_two_argument_and_returns_an_array(): void
    {
        $bookId = (string) Str::uuid();
        $accountGroupId = (string) Str::uuid();

        $accountGroupList = $this->accountGroup->searchBookForExporting($bookId, $accountGroupId);

        $this->assertTrue(is_array($accountGroupList));
    }
}
