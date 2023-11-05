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

class ImportBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookkeeping:importbooks {userId} {sourceUrl} {token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import books from another BookKeeping site';

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
        $sourceSiteUrl = strval($this->argument('sourceUrl'));
        $accessToken = strval($this->argument('token'));

        $user = User::find($userId); /* @phpstan-ignore-line */
        Auth::login($user);
        $this->info('Importing...');
        [$status, $importResult] = $service->importBooks($sourceSiteUrl, $accessToken);
        $result = json_encode($importResult, JSON_PRETTY_PRINT);

        $this->line('sourceUrl: '.$sourceSiteUrl);
        $this->line('status:    '.strval($status));
        $this->line('"result": '.$result);
    }
}
