<?php

namespace App\Console\Commands;

use App\DataProvider\Eloquent\AccountGroupRepository;
use App\DataProvider\Eloquent\AccountRepository;
use App\DataProvider\Eloquent\BookRepository;
use App\DataProvider\Eloquent\PermissionRepository;
use App\DataProvider\Eloquent\SlipEntryRepository;
use App\DataProvider\Eloquent\SlipRepository;
use App\Models\User;
use App\Service\AccountMigrationService;
use App\Service\BookKeepingMigration;
use App\Service\BookKeepingMigrationTools;
use App\Service\BookMigrationService;
use App\Service\SlipMigrationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class LoadBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookkeeping:loadbooks {userId} {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Books';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $tools = new BookKeepingMigrationTools();
        $service = new BookKeepingMigration(
            new BookMigrationService(new BookRepository(), new PermissionRepository, $tools),
            new AccountMigrationService(new AccountRepository(), new AccountGroupRepository(), $tools),
            new SlipMigrationService(new SlipRepository(), new SlipEntryRepository(), $tools),
            $tools
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

        $user = User::find($userId); /* @phpstan-ignore-line */
        Auth::login($user);
        $this->info('Loading...');

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
