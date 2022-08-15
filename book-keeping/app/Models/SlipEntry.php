<?php

namespace App\Models;

class SlipEntry extends BookKeepingBasicModel
{
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
