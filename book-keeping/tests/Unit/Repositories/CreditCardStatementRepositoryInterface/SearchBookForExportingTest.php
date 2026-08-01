<?php

namespace Tests\Unit\Repositories\CreditCardStatementRepositoryInterface;

use App\Repositories\Eloquent\CreditCardStatementRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchBookForExportingTest extends TestCase
{
    use RefreshDatabase;

    protected $creditCardStatement;

    public function setUp(): void
    {
        parent::setUp();
        $this->creditCardStatement = new CreditCardStatementRepository();
    }

    public function test_it_takes_one_argument_and_returns_an_array(): void
    {
        $bookId = (string) Str::uuid();

        $creditCardStatementList = $this->creditCardStatement->searchBookForExporting($bookId);

        $this->assertTrue(is_array($creditCardStatementList));
    }

    public function test_it_takes_two_argument_and_returns_an_array(): void
    {
        $bookId = (string) Str::uuid();
        $creditCardStatementId = (string) Str::uuid();

        $creditCardStatementList = $this->creditCardStatement->searchBookForExporting($bookId, $creditCardStatementId);

        $this->assertTrue(is_array($creditCardStatementList));
    }
}
