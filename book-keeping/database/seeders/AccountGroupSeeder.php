<?php

namespace Database\Seeders;

use App\Models\DataProvider\Eloquent\AccountGroup;
use App\Models\DataProvider\Eloquent\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountGroupSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = Book::all();
        foreach ($books as $book) {
            AccountGroup::factory()
                ->count(8)
                ->sequence(
                    ['account_type' => 'asset', 'is_current' => true],
                    ['account_type' => 'asset', 'is_current' => false],
                    ['account_type' => 'liability', 'is_current' => true],
                    ['account_type' => 'liability', 'is_current' => false],
                    ['account_type' => 'expense', 'is_current' => false],
                    ['account_type' => 'expense', 'is_current' => false],
                    ['account_type' => 'revenue', 'is_current' => false],
                    ['account_type' => 'revenue', 'is_current' => false],
                )
                ->create([
                    'book_id' => $book->book_id,
                ]);
        }
    }
}
