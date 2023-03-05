<?php

namespace App\Models;

/**
 * App\Models\Slip.
 *
 * @property string $slip_id
 * @property string $book_id
 * @property string $slip_outline
 * @property string|null $slip_memo
 * @property string $date
 * @property bool $is_draft
 * @property int|null $display_order
 */
class Slip extends BookKeepingBasicModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bk2_0_slips';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'slip_id';
}
