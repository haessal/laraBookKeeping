<?php

namespace App\Models;

/**
 * App\Models\Permission.
 *
 * @property string $permission_id
 * @property int $permitted_user
 * @property string $readable_book
 * @property bool $modifiable
 * @property bool $is_owner
 * @property bool $is_default
 * @property int|null $display_order
 */
class Permission extends BookKeepingBasicModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bk2_0_permissions';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'permission_id';
}
