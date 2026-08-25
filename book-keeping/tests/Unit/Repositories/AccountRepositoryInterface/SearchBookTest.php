<?php

namespace Tests\Unit\Repositories\AccountRepositoryInterface;

use App\Repositories\Eloquent\AccountRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchBookTest extends TestCase
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
        $bookId = (string) Str::uuid();

        $accountList = $this->account->searchBook($bookId);

        $this->assertTrue(is_array($accountList));
    }
}
