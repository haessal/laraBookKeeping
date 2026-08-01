<?php

namespace Tests\Unit\Repositories\CreditCardStatementRepositoryInterface;

use App\Repositories\Eloquent\CreditCardStatementRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateForImportingTest extends TestCase
{
    use RefreshDatabase;

    protected $creditCardStatement;

    public function setUp(): void
    {
        parent::setUp();
        $this->creditCardStatement = new CreditCardStatementRepository();
    }

    public function test_it_takes_one_argument_and_returns_nothing(): void
    {
        $newCreditCardStatement = [
            'credit_card_statement_id' => (string) Str::uuid(),
            'book_id' => (string) Str::uuid(),
            'credit_card_statement_outline' => 'outline128',
            'credit_card_statement_memo' => 'memo129',
            'date' => '2024-06-20',
            'display_order' => 0,
            'deleted' => false,
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->creditCardStatement->updateForImporting($newCreditCardStatement);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(true);
    }
}
