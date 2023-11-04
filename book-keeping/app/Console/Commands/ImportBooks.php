<?php

namespace App\Console\Commands;

use App\DataProvider\Eloquent\AccountGroupRepository;
use App\DataProvider\Eloquent\AccountRepository;
use App\DataProvider\Eloquent\BookRepository;
use App\DataProvider\Eloquent\BudgetRepository;
use App\DataProvider\Eloquent\PermissionRepository;
use App\DataProvider\Eloquent\SlipEntryRepository;
use App\DataProvider\Eloquent\SlipRepository;
use App\Models\User;
use App\Service\AccountService;
use App\Service\BookKeepingService;
use App\Service\BookService;
use App\Service\BudgetService;
use App\Service\SlipService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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
        $service = new BookKeepingService(
            new BookService(new BookRepository(), new PermissionRepository),
            new AccountService(new AccountRepository(), new AccountGroupRepository()),
            new BudgetService(new BudgetRepository),
            new SlipService(new SlipRepository(), new SlipEntryRepository())
        );
        $userId = intval($this->argument('userId'));
        $sourceSiteUrl = strval($this->argument('sourceUrl'));
        $accessToken = strval($this->argument('token'));

        $user = User::find($userId); /* @phpstan-ignore-line */
        Auth::login($user);
        $this->info('Start importting...');
        [$status, $importResult] = $service->importBooks($sourceSiteUrl, $accessToken);
        $result = json_encode($importResult, JSON_PRETTY_PRINT);

        $this->line('sourceUrl: '.$sourceSiteUrl);
        $this->line('status:    '.strval($status));
        $this->line('"result": '.$result);
    }
}
