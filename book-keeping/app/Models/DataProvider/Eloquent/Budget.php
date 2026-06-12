<?php

namespace App\Models\DataProvider\Eloquent;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['book_id', 'account_code', 'date', 'amount'])]
class Budget extends BookKeepingBasicModel
{
    /** @use HasFactory<\Database\Factories\DataProvider\Eloquent\BudgetFactory> */
    use HasFactory;

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
