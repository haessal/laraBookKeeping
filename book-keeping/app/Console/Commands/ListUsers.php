<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('user:list')]
#[Description('List all users')]
class ListUsers extends Command
{
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $users = User::all(['id', 'name', 'email', 'is_admin', 'email_verified_at', 'created_at']);

        if ($users->isEmpty()) {
            $this->warn('No users found.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Name', 'Email', 'Is Admin', 'Email Verified At', 'Created At'],
            $users->map(fn (User $user) => [
                $user->id,
                $user->name,
                $user->email,
                $user->is_admin ? 'Yes' : 'No',
                $this->formatDate($user->email_verified_at),
                $this->formatDate($user->created_at),
            ])->toArray()
        );

        $this->info("Total: {$users->count()} user(s).");

        return self::SUCCESS;
    }

    private function formatDate(?string $date): string
    {
        return $date ? Carbon::parse($date)->format('Y-m-d H:i:s') : '-';
    }
}
