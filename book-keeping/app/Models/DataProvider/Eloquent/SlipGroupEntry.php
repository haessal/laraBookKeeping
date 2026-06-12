<?php

namespace App\Models\DataProvider\Eloquent;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['slip_group_id', 'related_slip'])]
class SlipGroupEntry extends BookKeepingBasicModel
{
    /** @use HasFactory<\Database\Factories\DataProvider\Eloquent\SlipGroupEntryFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bk2_0_slip_group_entries';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'slip_group_entry_id';
}
