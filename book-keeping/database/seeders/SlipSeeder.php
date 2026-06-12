<?php

namespace Database\Seeders;

use App\Models\DataProvider\Eloquent\Book;
use App\Models\DataProvider\Eloquent\Slip;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SlipSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = Book::all();
        foreach ($books as $book) {
            Slip::factory(10)
                ->create([
                    'book_id' => $book->book_id,
                    'is_draft' => false,
                ]);
        }
    }
}
