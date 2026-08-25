<?php

namespace Tests\Unit\Repositories\AccountGroupRepositoryInterface;

use App\Repositories\Eloquent\AccountGroupRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchBookTest extends TestCase
{
    use RefreshDatabase;

    /** @var AccountGroupRepository */
    protected $accountGroup;

    public function setUp(): void
    {
        parent::setUp();
        $this->accountGroup = new AccountGroupRepository();
    }

    public function test_it_takes_one_argument_and_returns_an_array(): void
    {
        $bookId = (string) Str::uuid();

        $accountGroupList = $this->accountGroup->searchBook($bookId);

        $this->assertTrue(is_array($accountGroupList));
    }
}
