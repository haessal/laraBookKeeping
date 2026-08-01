<?php

namespace Tests\Unit\Repositories\CreditCardStatementRepositoryInterface;

use App\Repositories\Eloquent\CreditCardStatementRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchBookTest extends TestCase
{
    use RefreshDatabase;

    protected $creditCardStatement;

    public function setUp(): void
    {
        parent::setUp();
        $this->creditCardStatement = new CreditCardStatementRepository();
    }

    public function test_it_takes_two_arguments_and_returns_an_array(): void
    {
        $bookId = (string) Str::uuid();
        $creditCardStatementId = (string) Str::uuid();

        $creditCardStatementList = $this->creditCardStatement->searchBook($bookId, $creditCardStatementId);

        $this->assertTrue(is_array($creditCardStatementList));
    }
}
