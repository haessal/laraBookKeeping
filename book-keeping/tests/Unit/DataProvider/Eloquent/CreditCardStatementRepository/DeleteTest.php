<?php

namespace Tests\Unit\DataProvider\Eloquent\CreditCardStatementRepository;

use App\DataProvider\Eloquent\CreditCardStatementRepository;
use App\Models\CreditCardStatement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    protected $creditCardStatement;

    public function setUp(): void
    {
        parent::setUp();
        $this->creditCardStatement = new CreditCardStatementRepository();
    }

    public function test_one_record_is_soft_deleted(): void
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline27';
        $memo = 'memo28';
        $date = '2024-07-29';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $creditCardStatementId = CreditCardStatement::factory()->create([
            'book_id' => $bookId,
            'credit_card_statement_outline' => $outline,
            'credit_card_statement_memo' => $memo,
            'date' => $date,
        ])->credit_card_statement_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->creditCardStatement->delete($creditCardStatementId);

        $this->assertSoftDeleted('bk2_0_credit_card_statements', [
            'credit_card_statement_id' => $creditCardStatementId,
            'book_id' => $bookId,
            'credit_card_statement_outline' => $outline,
            'credit_card_statement_memo' => $memo,
            'date' => $date,
        ]);
    }
}
