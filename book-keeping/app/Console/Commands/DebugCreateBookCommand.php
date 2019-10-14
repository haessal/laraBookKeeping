<?php

namespace App\Console\Commands;

use App\DataProvider\Eloquent\BookRepository;
use App\DataProvider\Eloquent\PermissionRepository;
use App\Service\BookService;
use Illuminate\Console\Command;

class DebugCreateBookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:createBook {userId} {title}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Try making code';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $book = new BookService(new BookRepository(), new PermissionRepository());
        $userId = $this->argument('userId');
        $title = $this->argument('title');

        $bookId = $book->createBook($userId, $title);

        $this->comment($userId);
        $this->comment($bookId);
    }
}
