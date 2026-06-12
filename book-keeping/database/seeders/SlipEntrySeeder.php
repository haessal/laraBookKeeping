<?php

namespace Database\Seeders;

use App\Models\DataProvider\Eloquent\Book;
use App\Models\DataProvider\Eloquent\Slip;
use App\Models\DataProvider\Eloquent\SlipEntry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SlipEntrySeeder extends Seeder
{
    use withoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = Book::all();
        foreach ($books as $book) {
            $slips = Slip::where('book_id', $book->book_id)->get();
            $accounts = $book->accountGroups()->with('accounts')->get()->pluck('accounts')->flatten();
            foreach ($slips as $slip) {
                $created_entries = SlipEntry::factory()
                    ->count(rand(1, 5))
                    ->create([
                        'slip_id' => $slip->slip_id,
                        'debit' => $accounts->random()->account_id,
                        'credit' => $accounts->random()->account_id,
                    ]);

                // Ensure that debit and credit accounts are not the same
                foreach ($created_entries as $entry) {
                    while ($entry->debit === $entry->credit) {
                        $entry->credit = $accounts->random()->account_id;
                    }
                    $entry->save();
                }

                // if debit or credit account is credit card account, set credit_card_statement_id
                $credit_card_statement_id = $book->creditCardStatements()->inRandomOrder()->first()->credit_card_statement_id;
                foreach ($created_entries as $entry) {
                    $debit = $accounts->where('account_id', $entry->debit)->first();
                    $credit = $accounts->where('account_id', $entry->credit)->first();
                    if ($debit && $debit->is_credit_card) {
                        $entry->credit_card_statement_id = $credit_card_statement_id;
                    }
                    if ($credit && $credit->is_credit_card) {
                        $entry->credit_card_statement_id = $credit_card_statement_id;
                    }
                    $entry->save();
                }
            }
        }
    }
}
