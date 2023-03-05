<?php

namespace App\Models;

/**
 * App\Models\SlipEntry.
 *
 * @property string $slip_entry_id
 * @property string $slip_id
 * @property string $debit
 * @property string $credit
 * @property int $amount
 * @property string $client
 * @property string $outline
 * @property int|null $display_order
 */
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
