<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(BookSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(AccountGroupSeeder::class);
        $this->call(AccountSeeder::class);
        $this->call(CreditCardStatementSeeder::class);
        $this->call(SlipSeeder::class);
        $this->call(SlipEntrySeeder::class);
    }
}
