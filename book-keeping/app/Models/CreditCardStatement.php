<?php

namespace App\Models;

/**
 * App\Models\CreditCardStatement.
 *
 * @property string $credit_card_statement_id
 * @property string $book_id
 * @property string $credit_card_statement_outline
 * @property string|null $credit_card_statement_memo
 * @property string $date
 * @property int|null $display_order
 */
class CreditCardStatement extends BookKeepingBasicModel
{
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
