<?php

namespace Database\Seeders;

use App\Models\DataProvider\Eloquent\Book;
use App\Models\DataProvider\Eloquent\CreditCardStatement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreditCardStatementSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = Book::all();
        foreach ($books as $book) {
            CreditCardStatement::factory(1)
                ->create([
                    'book_id' => $book->book_id,
                ]);
        }
    }
}
