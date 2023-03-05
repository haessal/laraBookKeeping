<?php

namespace App\Models;

/**
 * App\Models\Budget.
 *
 * @property string $budget_id
 * @property string $book_id
 * @property string $account_code
 * @property string $date
 * @property int $amount
 * @property int|null $display_order
 */
class Budget extends BookKeepingBasicModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bk2_0_budgets';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'budget_id';
}
