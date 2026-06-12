<?php

namespace Database\Seeders;

use App\Models\DataProvider\Eloquent\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Book::factory(4)->create();
    }
}
