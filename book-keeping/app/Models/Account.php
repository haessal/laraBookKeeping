<?php

namespace App\Models;

/**
 * App\Models\Account.
 *
 * @property string $account_id
 * @property string $account_group_id
 * @property string $account_title
 * @property string $description
 * @property bool $selectable
 * @property int|null $bk_uid
 * @property int|null $account_bk_code
 * @property int|null $display_order
 */
class Account extends BookKeepingBasicModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bk2_0_accounts';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'account_id';
}
