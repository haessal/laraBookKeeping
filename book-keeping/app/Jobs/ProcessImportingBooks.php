<?php

namespace App\Jobs;

use App\Mail\ImportingCompleted;
use App\Models\User;
use App\Service\BookKeepingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ProcessImportingBooks implements ShouldQueue, ShouldBeEncrypted
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * BookKeeping service instance.
     *
     * @var \App\Service\BookKeepingService
     */
    protected $BookKeeping;

    /**
     * The URL of the import source.
     *
     * @var string
     */
    protected $sourceUrl;

    /**
     * The token to access the source site.
     *
     * @var string
     */
    protected $accessToken;

    /**
     * The user processing the import.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  string  $sourceUrl
     * @param  string  $accessToken
     * @return void
     */
    public function __construct(User $user, BookKeepingService $BookKeeping, $sourceUrl, $accessToken)
    {
        $this->user = $user;
        $this->BookKeeping = $BookKeeping;
        $this->sourceUrl = $sourceUrl;
        $this->accessToken = $accessToken;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Auth::login($this->user);
        [$status, $importResult] = $this->BookKeeping->importBooks($this->sourceUrl, $this->accessToken);
        $result = json_encode($importResult, JSON_PRETTY_PRINT);
        Mail::to(Auth::user())->send(new ImportingCompleted($this->sourceUrl, $status, strval($result)));
    }
}
