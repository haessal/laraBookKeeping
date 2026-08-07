<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Repositories\Eloquent\AccountGroupRepository;
use App\Repositories\Eloquent\AccountRepository;
use App\Repositories\Eloquent\BookRepository;
use App\Repositories\Eloquent\CreditCardStatementRepository;
use App\Repositories\Eloquent\PermissionRepository;
use App\Repositories\Eloquent\SlipEntryRepository;
use App\Repositories\Eloquent\SlipRepository;
use App\Service\AccountMigrationLoaderService;
use App\Service\BookKeepingMigrationLoader;
use App\Service\BookKeepingMigrationTools;
use App\Service\BookKeepingMigrationValidator;
use App\Service\BookMigrationLoaderService;
use App\Service\CreditCardStatementMigrationLoaderService;
use App\Service\SlipMigrationLoaderService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

#[Signature('book:import {userId} {file}')]
#[Description('Import books and assign them to the specified user')]
class ImportBooks extends Command
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $tools = new BookKeepingMigrationTools();
        $validator = new BookKeepingMigrationValidator();
        $service = new BookKeepingMigrationLoader(
            new BookMigrationLoaderService(new BookRepository(), new PermissionRepository, $tools, $validator),
            new AccountMigrationLoaderService(new AccountRepository(), new AccountGroupRepository(), $tools, $validator),
            new SlipMigrationLoaderService(new SlipRepository(), new SlipEntryRepository(), $tools, $validator),
            new CreditCardStatementMigrationLoaderService(new CreditCardStatementRepository(), $tools, $validator),
        );
        $userId = intval($this->argument('userId'));
        $file = strval($this->argument('file'));

        if (! file_exists($file)) {
            $this->error('File not found: '.$file);

            return;
        }

        $contentsJson = file_get_contents($file);
        if ($contentsJson === false) {
            $this->error('Unable to read file: '.$file);

            return;
        }

        $user = User::find($userId);
        if (! $user) {
            $this->error('User not found: '.$userId);

            return;
        }
        Auth::login($user);
        $this->info('Importing books...');

        /** @var array<string, mixed> $contents */
        $contents = json_decode($contentsJson, true);
        [$status, $importResult, $message] = $service->loadBooks($contents);
        $result = json_encode($importResult, JSON_PRETTY_PRINT);

        $this->line('status:    '.strval($status));
        if (isset($message)) {
            $this->error($message);
        }
        $this->line('"result": '.$result);
    }
}
