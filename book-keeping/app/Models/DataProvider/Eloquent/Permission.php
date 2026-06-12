<?php

namespace App\Models\DataProvider\Eloquent;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['permitted_user', 'readable_book', 'modifiable', 'is_owner', 'is_default'])]
class Permission extends BookKeepingBasicModel
{
    /** @use HasFactory<\Database\Factories\DataProvider\Eloquent\PermissionFactory> */
    use HasFactory;

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
