<?php

namespace App\Console\Commands;

use App\DataProvider\Eloquent\AccountGroupRepository;
use App\DataProvider\Eloquent\AccountRepository;
use App\DataProvider\Eloquent\BookRepository;
use App\DataProvider\Eloquent\BudgetRepository;
use App\DataProvider\Eloquent\PermissionRepository;
use App\DataProvider\Eloquent\SlipEntryRepository;
use App\DataProvider\Eloquent\SlipRepository;
use App\Service\AccountService;
use App\Service\BookService;
use App\Service\BudgetService;
use App\Service\OldBookService;
use App\Service\SlipService;
use Illuminate\Console\Command;

class DebugMigrateBookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:migrateBook {oldId} {userId} {title}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Book from old DB version';

    /**
     * Account service instance.
     *
     * @var \App\Service\AccountService 
     */
    private $account;

     /**
     * Book service instance.
     *
     * @var \App\Service\BookService 
     */
    private $book;

         /**
     * Book service instance.
     *
     * @var \App\Service\BudgetService 
     */
    private $budget;

    /**
     * Old book service instance.
     *
     * @var \App\Service\OldBookService 
     */
    private $oldbook;

    /**
     * Slip service instance.
     *
     * @var \App\Service\SlipService 
     */
    private $slip;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->book = new BookService(new BookRepository(), new PermissionRepository());
        $this->oldbook = new OldBookService();
        $this->account = new AccountService(new AccountRepository(), new AccountGroupRepository());
        $this->budget = new BudgetService(new BudgetRepository());
        $this->slip = new SlipService(new SlipRepository(), new SlipEntryRepository());
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $oldId = $this->argument('oldId');
        $userId = $this->argument('userId');
        $title = $this->argument('title');

        $bookId = $this->book->createBook($userId, $title);
        $accountIds = $this->migrateAccount($oldId, $bookId);
        $this->migrateBudget($oldId, $bookId, $accountIds);
        $this->migrateJarnal($oldId, $bookId, $accountIds);

        $this->comment('oldId: ' . $oldId);
        $this->comment('userId: ' . $userId);
        $this->comment('title: ' . $title);
        $this->comment('bookId: ' . $bookId);
    }

    private function getAccountGroupCode(int $accountCode)
    {
        return $accountCode - ($accountCode % 100);
    }

    private function getAccountType(int $accountCode)
    {
        $accountTypes = [
            1000 => AccountService::ACCOUNT_TYPE_ASSET,
            2000 => AccountService::ACCOUNT_TYPE_LIABILITY,
            4000 => AccountService::ACCOUNT_TYPE_EXPENSE,
            5000 => AccountService::ACCOUNT_TYPE_REVENUE
        ];
        $accountTypeCode = $accountCode - ($accountCode % 1000);

        return $accountTypes[$accountTypeCode];
    }

    private function isCurrentAccount(int $accountCode)
    {
        switch ($accountCode) {
            case 1100:
            case 1200:
            case 2200:
            case 2300:
                return true;
            default:
                return false;
        }
    }

    private function migrateAccount($oldId, $bookId) : array
    {
        $accountGroupIds = [];
        $accountIds = [];
        $oldAccounts = $this->oldbook->retrieveAccounts($oldId);
        foreach ($oldAccounts as &$account) {
            $this->comment('code: ' . $account->code);
            if ($account->code % 1000 == 0) {
            } else if ($account->code % 100 == 0) {
                $accountGroupIds[$account->code] = $this->account->createAccountGroup(
                    $bookId,
                    $this->getAccountType($account->code),
                    $account->title,
                    $this->isCurrentAccount($account->code),
                    $account->uid,
                    $account->code
                );
            }else {
                $accountGroupCode = $this->getAccountGroupCode($account->code);
                $accountIds[$account->code] = $this->account->createAccount(
                    $accountGroupIds[$accountGroupCode],
                    $account->title,
                    $account->description,
                    $account->uid,
                    $account->code
                );
            }
        }

        return $accountIds;
    }

    private function migrateBudget($oldId, $bookId, $accountIds)
    {
        $oldBudgets = $this->oldbook->retrieveBudgets($oldId);
        foreach ($oldBudgets as &$budget) {
            $this->budget->createBudget(
                $bookId,
                $accountIds[$budget->code],
                $budget->date,
                $budget->amount
            );
        } 
    }

    private function migrateJarnal($oldId, $bookId, $accountIds)
    {
        $oldSlipNoList = $this->oldbook->retrieveSlipNoList($oldId);
        foreach ($oldSlipNoList as &$oldSlipNo) {
            $this->comment('slipNo: ' . $oldSlipNo->slipno);
            $oldSlipEntries = $this->oldbook->retrieveSlip($oldId, $oldSlipNo->slipno);

            $outline = $oldSlipEntries[0]->outline;
            $date = $oldSlipEntries[0]->date;
            $slipEntries = [];
            foreach ($oldSlipEntries as &$oldSlipEntry) {
                $slipEntries[] = [
                    'debit' => $accountIds[$oldSlipEntry->debit], 
                    'credit' => $accountIds[$oldSlipEntry->credit],
                    'amount' => $oldSlipEntry->amount, 
                    'client' => $oldSlipEntry->client,
                    'outline' => $oldSlipEntry->outline
                ];
            }
            $slipId = $this->slip->createSlipAsDraft($bookId, $outline, $date, $slipEntries);
            $this->slip->submitSlip($slipId);
        }
    }
}