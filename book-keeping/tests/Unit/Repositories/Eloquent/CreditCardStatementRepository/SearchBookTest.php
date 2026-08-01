<?php

namespace Tests\Unit\Repositories\Eloquent\CreditCardStatementRepository;

use App\Models\DataProvider\Eloquent\CreditCardStatement;
use App\Repositories\Eloquent\CreditCardStatementRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    public function test_the_returned_array_has_keys_as_credit_card_statement(): void
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline127';
        $memo = 'memo128';
        $date = '2024-06-29';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $creditCardStatementId = CreditCardStatement::factory()->create([
            'book_id' => $bookId,
            'credit_card_statement_outline' => $outline,
            'credit_card_statement_memo' => $memo,
            'date' => $date,
        ])->credit_card_statement_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $creditCardStatementList = $this->creditCardStatement->searchBook($bookId, $creditCardStatementId);

        $this->assertFalse(count($creditCardStatementList) === 0);
        if (! (count($creditCardStatementList) === 0)) {
            $this->assertSame([
                'credit_card_statement_id',
                'credit_card_statement_outline',
                'credit_card_statement_memo',
                'date',
            ], array_keys($creditCardStatementList[0]));
        }
    }
}
