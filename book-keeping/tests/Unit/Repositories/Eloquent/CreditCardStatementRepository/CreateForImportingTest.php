<?php

namespace Tests\Unit\Repositories\Eloquent\CreditCardStatementRepository;

use App\Repositories\Eloquent\CreditCardStatementRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateForImportingTest extends TestCase
{
    use RefreshDatabase;

    protected $creditCardStatement;

    public function setUp(): void
    {
        parent::setUp();
        $this->creditCardStatement = new CreditCardStatementRepository();
    }

    public function test_one_record_is_created(): void
    {
        $creditCardStatementId = (string) Str::uuid();
        $bookId = (string) Str::uuid();
        $outline = 'outline27';
        $memo = 'memo28';
        $date = '2024-07-09';
        $displayOrder = 1;
        $deleted = false;
        $newCreditCardStatement = [
            'credit_card_statement_id' => $creditCardStatementId,
            'book_id' => $bookId,
            'credit_card_statement_outline' => $outline,
            'credit_card_statement_memo' => $memo,
            'date' => $date,
            'display_order' => $displayOrder,
            'deleted' => $deleted,
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->creditCardStatement->createForImporting($newCreditCardStatement);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_credit_card_statements', [
            'credit_card_statement_id' => $creditCardStatementId,
            'book_id' => $bookId,
            'credit_card_statement_outline' => $outline,
            'credit_card_statement_memo' => $memo,
            'date' => $date,
            'display_order' => $displayOrder,
            'deleted_at' => null,
        ]);
    }

    public function test_one_record_is_created_and_then_deleted(): void
    {
        $creditCardStatementId = (string) Str::uuid();
        $bookId = (string) Str::uuid();
        $outline = 'outline27';
        $memo = 'memo28';
        $date = '2024-07-09';
        $displayOrder = 1;
        $deleted = true;
        $newCreditCardStatement = [
            'credit_card_statement_id' => $creditCardStatementId,
            'book_id' => $bookId,
            'credit_card_statement_outline' => $outline,
            'credit_card_statement_memo' => $memo,
            'date' => $date,
            'display_order' => $displayOrder,
            'deleted' => $deleted,
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->creditCardStatement->createForImporting($newCreditCardStatement);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertSoftDeleted('bk2_0_credit_card_statements', [
            'credit_card_statement_id' => $creditCardStatementId,
            'book_id' => $bookId,
            'credit_card_statement_outline' => $outline,
            'credit_card_statement_memo' => $memo,
            'date' => $date,
            'display_order' => $displayOrder,
        ]);
    }
}
