<?php

namespace Tests\Unit\DataProvider\SlipEntryRepositoryInterface;

use App\DataProvider\Eloquent\SlipEntryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchBookWithCreditCardStatementTest extends TestCase
{
    use RefreshDatabase;

    protected $slipEntry;

    public function setUp(): void
    {
        parent::setUp();
        $this->slipEntry = new SlipEntryRepository();
    }

    public function test_it_takes_two_arguments_and_returns_an_array(): void
    {
        $bookId = (string) Str::uuid();
        $creditCardStatementId = (string) Str::uuid();

        $slipEntries = $this->slipEntry->searchBookWithCreditCardStatement($bookId, $creditCardStatementId);

        $this->assertTrue(is_array($slipEntries));
    }
}
