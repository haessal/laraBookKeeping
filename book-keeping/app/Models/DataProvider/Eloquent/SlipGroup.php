<?php

namespace App\Models\DataProvider\Eloquent;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Get the slip group entries for the slip group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<SlipGroupEntry, $this>
     */
    public function slipGroupEntries(): HasMany
    {
        return $this->hasMany(SlipGroupEntry::class, 'slip_group_id', 'slip_group_id');
    }
}
