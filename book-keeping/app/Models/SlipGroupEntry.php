<?php

namespace App\Models;

/**
 * App\Models\SlipGroupEntry.
 *
 * @property string $slip_group_entry_id
 * @property string $slip_group_id
 * @property string $related_slip
 * @property int|null $display_order
 */
class SlipGroupEntry extends BookKeepingBasicModel
{
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
