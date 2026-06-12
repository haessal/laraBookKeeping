<?php

namespace App\Models\DataProvider\Eloquent;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['book_id', 'credit_card_statement_outline', 'credit_card_statement_memo', 'date'])]
class CreditCardStatement extends BookKeepingBasicModel
{
    /** @use HasFactory<\Database\Factories\DataProvider\Eloquent\CreditCardStatementFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bk2_0_credit_card_statements';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'credit_card_statement_id';
}
