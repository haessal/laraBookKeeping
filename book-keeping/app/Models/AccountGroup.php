<?php

namespace App\Models;

/**
 * App\Models\AccountGroup.
 *
 * @property string $account_group_id
 * @property string $book_id
 * @property string $account_type
 * @property string $account_group_title
 * @property int|null $bk_uid
 * @property int|null $account_group_bk_code
 * @property bool $is_current
 * @property int|null $display_order
 */
class AccountGroup extends BookKeepingBasicModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bk2_0_account_groups';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'account_group_id';
}
