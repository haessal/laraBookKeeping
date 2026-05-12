<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

#[Signature('user:register {--name= : user name} {--email= : email address} {--password= : password}')]
#[Description('Register a new user')]
class RegisterUser extends Command
{
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        /** @var string $name */
        $name = $this->option('name') ?? $this->ask('Name');

        /** @var string $email */
        $email = $this->option('email') ?? $this->ask('Email');

        /** @var string $password */
        $password = $this->option('password') ?? $this->secret('Password');

        // check for duplicate email or name
        if (User::where('name', $name)->exists()) {
            $this->error("User with name {$name} already exists.");

            return self::FAILURE;
        }
        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists.");

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => User::count() == 0, // First user is admin
        ]);
        $user->markEmailAsVerified();

        $this->info('User registered successfully.');
        $this->table(
            ['Name', 'Email', 'Is Admin', 'Email Verified At'],
            [[
                $user->name,
                $user->email,
                $user->is_admin ? 'Yes' : 'No',
                Carbon::parse($user->email_verified_at)->format('Y-m-d H:i:s'),
            ]]
        );

        return self::SUCCESS;
    }
}
