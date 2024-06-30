<?php

namespace Tests\Unit\DataProvider\Eloquent\CreditCardStatementRepository;

use App\DataProvider\Eloquent\CreditCardStatementRepository;
use App\Models\CreditCardStatement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    public function test_the_returned_array_has_keys_as_exported_credit_card_statement(): void
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline127';
        $memo = 'memo128';
        $date = '2024-06-29';
        $displayOrder = 1;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        CreditCardStatement::factory()->create([
            'book_id' => $bookId,
            'credit_card_statement_outline' => $outline,
            'credit_card_statement_memo' => $memo,
            'date' => $date,
            'display_order' => $displayOrder,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $creditCardStatementList = $this->creditCardStatement->searchBookForExporting($bookId);

        $this->assertFalse(count($creditCardStatementList) === 0);
        if (! (count($creditCardStatementList) === 0)) {
            $this->assertSame([
                'credit_card_statement_id',
                'book_id',
                'credit_card_statement_outline',
                'credit_card_statement_memo',
                'date',
                'display_order',
                'created_at',
                'updated_at',
                'deleted_at',
            ], array_keys($creditCardStatementList[0]));
        }
    }

    public function test_the_returned_array_has_keys_as_exported_credit_card_statement_even_if_it_is_called_with_credit_card_statement_id(): void
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline62';
        $memo = 'memo63';
        $date = '2024-06-04';
        $displayOrder = 1;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $creditCardStatementId = CreditCardStatement::factory()->create([
            'book_id' => $bookId,
            'credit_card_statement_outline' => $outline,
            'credit_card_statement_memo' => $memo,
            'date' => $date,
            'display_order' => $displayOrder,
        ])->credit_card_statement_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $creditCardStatementList = $this->creditCardStatement->searchBookForExporting($bookId, $creditCardStatementId);

        $this->assertFalse(count($creditCardStatementList) === 0);
        if (! (count($creditCardStatementList) === 0)) {
            $this->assertSame([
                'credit_card_statement_id',
                'book_id',
                'credit_card_statement_outline',
                'credit_card_statement_memo',
                'date',
                'display_order',
                'created_at',
                'updated_at',
                'deleted_at',
            ], array_keys($creditCardStatementList[0]));
        }
    }
}
