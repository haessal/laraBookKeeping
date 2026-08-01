<?php

namespace Tests\Unit\Repositories\Eloquent\CreditCardStatementRepository;

use App\Models\DataProvider\Eloquent\CreditCardStatement;
use App\Repositories\Eloquent\CreditCardStatementRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    protected $creditCardStatement;

    public function setUp(): void
    {
        parent::setUp();
        $this->creditCardStatement = new CreditCardStatementRepository();
    }

    public function test_one_record_is_updated(): void
    {
        $bookId = (string) Str::uuid();
        $outline = 'outlin327';
        $memo = 'outlin328';
        $date = '2024-05-29';
        $outline_updated = 'outlin427';
        $memo_updated = 'outlin428';
        $date_updated = '2024-06-29';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $creditCardStatementId = CreditCardStatement::factory()->create([
            'book_id' => $bookId,
            'credit_card_statement_outline' => $outline,
            'credit_card_statement_memo' => $memo,
            'date' => $date,
        ])->credit_card_statement_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->creditCardStatement->update($creditCardStatementId, [
            'outline' => $outline_updated,
            'memo' => $memo_updated,
            'date' => $date_updated,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_credit_card_statements', [
            'credit_card_statement_id' => $creditCardStatementId,
            'book_id' => $bookId,
            'credit_card_statement_outline' => $outline_updated,
            'credit_card_statement_memo' => $memo_updated,
            'date' => $date_updated,
        ]);
    }
}
