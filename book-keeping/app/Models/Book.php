<?php

namespace App\Models;

/**
 * App\Models\Book.
 *
 * @property string $book_id
 * @property string $book_name
 * @property int|null $display_order
 */
class Book extends BookKeepingBasicModel
{
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
}
