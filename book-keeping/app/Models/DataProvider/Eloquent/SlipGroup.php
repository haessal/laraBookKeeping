<?php

namespace App\Models\DataProvider\Eloquent;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['book_id', 'slip_group_outline', 'slip_group_memo'])]
class SlipGroup extends BookKeepingBasicModel
{
    /** @use HasFactory<\Database\Factories\DataProvider\Eloquent\SlipGroupFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bk2_0_slip_groups';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'slip_group_id';
}
