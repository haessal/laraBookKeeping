<?php

namespace App\Models;

/**
 * App\Models\SlipGroup.
 *
 * @property string $slip_group_id
 * @property string $book_id
 * @property string $slip_group_outline
 * @property string|null $slip_group_memo
 * @property int|null $display_order
 */
class SlipGroup extends BookKeepingBasicModel
{
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
