<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateVerifiedUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookkeeping:createuser {name} {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a verified user';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $name = strval($this->argument('name'));
        $email = strval($this->argument('email'));
        $password = strval($this->argument('password'));

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->save();
        $user->refresh();
        $user->email_verified_at = $user->created_at; /* @phpstan-ignore-line */
        $user->save();

        $this->info('The user has been created. (id: '.$user->id.', name: '.$user->name.')');
    }
}
