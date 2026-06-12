<?php

namespace App\Models\DataProvider\Eloquent;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['slip_id', 'debit', 'credit', 'amount', 'client', 'outline', 'credit_card_statement_id'])]
class SlipEntry extends BookKeepingBasicModel
{
    /** @use HasFactory<\Database\Factories\DataProvider\Eloquent\SlipEntryFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bk2_0_slip_entries';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'slip_entry_id';
}
