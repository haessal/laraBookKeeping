<?php

namespace App\Models\DataProvider\Eloquent;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['book_id', 'slip_outline', 'slip_memo', 'date', 'is_draft'])]
class Slip extends BookKeepingBasicModel
{
    /** @use HasFactory<\Database\Factories\DataProvider\Eloquent\SlipFactory> */
    use HasFactory;

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

    /**
     * Get the slip entries for the slip.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<SlipEntry, $this>
     */
    public function slipEntries(): HasMany
    {
        return $this->hasMany(SlipEntry::class, 'slip_id', 'slip_id');
    }
}
