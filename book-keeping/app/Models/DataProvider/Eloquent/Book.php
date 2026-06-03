<?php

namespace App\Models\DataProvider\Eloquent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\DataProvider\Eloquent\BookFactory> */
    use HasFactory;
}
