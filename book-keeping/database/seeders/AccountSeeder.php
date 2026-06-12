<?php

namespace Database\Seeders;

use App\Models\DataProvider\Eloquent\Account;
use App\Models\DataProvider\Eloquent\AccountGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accountGroups = AccountGroup::all();
        foreach ($accountGroups as $accountGroup) {
            Account::factory()
                ->count(3)
                ->sequence(
                    ['selectable' => true, 'is_credit_card' => false],
                    ['selectable' => true, 'is_credit_card' => true],
                    ['selectable' => false, 'is_credit_card' => false],
                )
                ->create([
                    'account_group_id' => $accountGroup->account_group_id,
                ]);
        }
    }
}
