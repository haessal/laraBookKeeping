<?php

namespace App\Models\DataProvider\Eloquent;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['book_name'])]
class Book extends BookKeepingBasicModel
{
    /** @use HasFactory<\Database\Factories\DataProvider\Eloquent\BookFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bk2_0_books';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'book_id';

    /**
     * Get the account groups for the book.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<AccountGroup, $this>
     */
    public function accountGroups(): HasMany
    {
        return $this->hasMany(AccountGroup::class, 'book_id', 'book_id');
    }

    /**
     * Get the slips for the book.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Slip, $this>
     */
    public function slips(): HasMany
    {
        return $this->hasMany(Slip::class, 'book_id', 'book_id');
    }

    /**
     * Get the credit card statements for the book.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<CreditCardStatement, $this>
     */
    public function creditCardStatements(): HasMany
    {
        return $this->hasMany(CreditCardStatement::class, 'book_id', 'book_id');
    }

    /**
     * Get the budgets for the book.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Budget, $this>
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class, 'book_id', 'book_id');
    }

    /**
     * Get the slip groups for the book.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<SlipGroup, $this>
     */
    public function slipGroups(): HasMany
    {
        return $this->hasMany(SlipGroup::class, 'book_id', 'book_id');
    }
}
