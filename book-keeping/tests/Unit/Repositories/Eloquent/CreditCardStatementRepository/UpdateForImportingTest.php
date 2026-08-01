<?php

namespace Tests\Unit\Repositories\Eloquent\CreditCardStatementRepository;

use App\Models\DataProvider\Eloquent\CreditCardStatement;
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

    public function test_one_record_is_updated(): void
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline27';
        $memo = 'memo28';
        $date = '2024-07-09';
        $displayOrder = 1;
        $deleted = false;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $creditCardStatementId = CreditCardStatement::factory()->create([
            'book_id' => (string) Str::uuid(),
            'credit_card_statement_outline' => 'outline35',
            'credit_card_statement_memo' => 'memo36',
            'date' => '2023-07-07',
            'display_order' => 2,
        ])->credit_card_statement_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
        $this->creditCardStatement->updateForImporting($newCreditCardStatement);
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

    public function test_one_record_is_updated_and_then_deleted(): void
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline69';
        $memo = 'memo70';
        $date = '2024-07-01';
        $displayOrder = 1;
        $deleted = true;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $creditCardStatementId = CreditCardStatement::factory()->create([
            'book_id' => (string) Str::uuid(),
            'credit_card_statement_outline' => 'outline77',
            'credit_card_statement_memo' => 'memo78',
            'date' => '2023-07-09',
            'display_order' => 2,
        ])->credit_card_statement_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
        $this->creditCardStatement->updateForImporting($newCreditCardStatement);
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

    public function test_one_record_is_updated_and_then_restored(): void
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline110';
        $memo = 'memo111';
        $date = '2024-07-26';
        $displayOrder = 1;
        $deleted = false;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $creditCardStatement = CreditCardStatement::factory()->create([
            'book_id' => (string) Str::uuid(),
            'credit_card_statement_outline' => 'outline118',
            'credit_card_statement_memo' => 'memo119',
            'date' => '2023-07-07',
            'display_order' => 2,
        ]);
        $creditCardStatementId = $creditCardStatement->credit_card_statement_id;
        $creditCardStatement->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
        $this->creditCardStatement->updateForImporting($newCreditCardStatement);
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

    public function test_one_record_is_updated_and_still_in_the_trash(): void
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline154';
        $memo = 'memo155';
        $date = '2024-07-25';
        $displayOrder = 1;
        $deleted = true;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $creditCardStatement = CreditCardStatement::factory()->create([
            'book_id' => (string) Str::uuid(),
            'credit_card_statement_outline' => 'outline162',
            'credit_card_statement_memo' => 'memo163',
            'date' => '2023-07-06',
            'display_order' => 2,
        ]);
        $creditCardStatementId = $creditCardStatement->credit_card_statement_id;
        $creditCardStatement->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
        $this->creditCardStatement->updateForImporting($newCreditCardStatement);
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
