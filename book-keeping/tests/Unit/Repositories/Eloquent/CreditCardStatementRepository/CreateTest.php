<?php

namespace Tests\Unit\Repositories\Eloquent\CreditCardStatementRepository;

use App\Repositories\Eloquent\CreditCardStatementRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /** @var CreditCardStatementRepository */
    protected $creditCardStatement;

    public function setUp(): void
    {
        parent::setUp();
        $this->creditCardStatement = new CreditCardStatementRepository();
    }

    public function test_one_record_is_created(): void
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline26';
        $memo = 'memo27';
        $date = '2024-07-28';

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $creditCardStatementId = $this->creditCardStatement->create($bookId, $outline, $memo, $date, null);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_credit_card_statements', [
            'credit_card_statement_id' => $creditCardStatementId,
            'book_id' => $bookId,
            'credit_card_statement_outline' => $outline,
            'credit_card_statement_memo' => $memo,
            'date' => $date,
        ]);
    }
}
