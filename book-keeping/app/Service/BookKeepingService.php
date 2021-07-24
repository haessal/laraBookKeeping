<?php

namespace App\Service;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class BookKeepingService
{
    /**
     * The origin date of the system.
     *
     * @var string
     */
    const ORIGIN_DATE = '1970-01-02';

    /**
     * Account service instance.
     *
     * @var \App\Service\AccountService
     */
    public $account;

    /**
     * Book service instance.
     *
     * @var \App\Service\BookService
     */
    public $book;

    /**
     * Budge service instance.
     *
     * @var \App\Service\BudgetService
     */
    public $budget;

    /**
     * Slip service instance.
     *
     * @var \App\Service\SlipService
     */
    public $slip;

    /**
     * Create a new BookKeepingService instance.
     *
     * @param \App\Service\BookService    $book
     * @param \App\Service\AccountService $account
     * @param \App\Service\BudgetService  $budget
     * @param \App\Service\SlipService    $slip
     */
    public function __construct(BookService $book, AccountService $account, BudgetService $budget, SlipService $slip)
    {
        $this->book = $book;
        $this->account = $account;
        $this->budget = $budget;
        $this->slip = $slip;
    }

    /**
     * Create a new slip.
     *
     * @param string $outline
     * @param string $date
     * @param array  $entries
     * @param string $memo
     * @param string $bookId
     *
     * @return string
     */
    public function createSlip(string $outline, string $date, array $entries, ?string $memo, string $bookId = null): string
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(Auth::id());
        }
        $slipId = $this->slip->createSlipAsDraft($bookId, $outline, $date, $entries, $memo);
        $this->slip->submitSlip($slipId);

        return $slipId;
    }

    /**
     * Add a new slip entry as draft.
     *
     * @param string $debit
     * @param string $client
     * @param string $outline
     * @param string $credit
     * @param int    $amount
     * @param string $bookId
     *
     * @return void
     */
    public function createSlipEntryAsDraft(string $debit, string $client, string $outline, string $credit, int $amount, string $bookId = null)
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(Auth::id());
        }
        $draftSlips = $this->slip->retrieveDraftSlips($bookId);
        if (empty($draftSlips)) {
            $date = new Carbon();
            $this->slip->createSlipAsDraft($bookId, $outline, $date->format('Y-m-d'), [
                ['debit' => $debit, 'client' => $client, 'outline' => $outline, 'credit' => $credit, 'amount' => $amount],
            ]);
        } else {
            $this->slip->createSlipEntry($draftSlips[0]['slip_id'], $debit, $credit, $amount, $client, $outline);
        }
    }

    /**
     * Delete the specified slip entry.
     *
     * @param string $slipEntryId
     *
     * @return void
     */
    public function deleteSlipEntryAsDraft(string $slipEntryId)
    {
        $slipId = $this->slip->retrieveSlipThatBound($slipEntryId);
        $this->slip->deleteSlipEntry($slipEntryId);
        $slipEntries = $this->slip->retrieveSlipEntriesBoundTo($slipId);
        if (empty($slipEntries)) {
            $this->slip->deleteSlip($slipId);
        }
    }

    /**
     * Retrieve account list.
     *
     * @param bool   $selectableOnly
     * @param string $bookId
     *
     * @return array
     */
    public function retrieveAccounts(bool $selectableOnly = false, string $bookId = null): array
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(Auth::id());
        }
        $accounts = $this->account->retrieveAccounts($bookId);

        $accounts_menu = [
            AccountService::ACCOUNT_TYPE_ASSET     => ['groups' => []],
            AccountService::ACCOUNT_TYPE_LIABILITY => ['groups' => []],
            AccountService::ACCOUNT_TYPE_EXPENSE   => ['groups' => []],
            AccountService::ACCOUNT_TYPE_REVENUE   => ['groups' => []],
        ];

        foreach ($accounts as $accountsKey => $accountsItem) {
            if ((!$selectableOnly) || ($accountsItem['selectable'] == true)) {
                if (!array_key_exists($accountsItem['account_group_id'], $accounts_menu[$accountsItem['account_type']]['groups'])) {
                    $accounts_menu[$accountsItem['account_type']]['groups'][$accountsItem['account_group_id']] = [
                        'title'        => $accountsItem['account_group_title'],
                        'isCurrent'    => $accountsItem['is_current'],
                        'bk_code'      => $accountsItem['account_group_bk_code'],
                        'createdAt'    => $accountsItem['account_group_created_at'],
                        'items'        => [],
                    ];
                }
                $accounts_menu[$accountsItem['account_type']]['groups'][$accountsItem['account_group_id']]['items'][$accountsKey] = [
                    'title'       => $accountsItem['account_title'],
                    'description' => $accountsItem['description'],
                    'selectable'  => $accountsItem['selectable'],
                    'bk_code'     => $accountsItem['account_bk_code'],
                    'createdAt'   => $accountsItem['created_at'],
                ];
            }
        }

        return $accounts_menu;
    }

    /**
     * Retrieve account list.
     *
     * @param string $bookId
     *
     * @return array
     */
    public function retrieveAccountsList(string $bookId = null): array
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(Auth::id());
        }
        $accounts = $this->account->retrieveAccounts($bookId);

        return $accounts;
    }

    /**
     * Retrieve available Books.
     *
     * @return array
     */
    public function retrieveAvailableBook(): array
    {
        $books = [];

        $bookList = $this->book->retrieveBookList(Auth::id());
        foreach ($bookList as $book) {
            $id = $book['book_id'];
            $owner = $this->book->ownerName($id);
            $books[] = ['id' => $id, 'owner' => $owner, 'name' => $book['book_name']];
        }

        return $books;
    }

    /**
     * Retrieve information about the specified book.
     *
     * @param string $bookId
     *
     * @return array | null
     */
    public function retrieveBookInformation(string $bookId): ?array
    {
        $information = null;

        $owner = $this->book->ownerName($bookId);
        $book = $this->book->retrieveInformation($bookId);
        if ((!empty($owner)) && (!empty($book))) {
            $information = ['id' => $bookId, 'owner' => $owner, 'name' => $book['book_name']];
        }

        return $information;
    }

    /**
     * Retrieve draft slips.
     *
     * @param string $bookId
     *
     * @return array
     */
    public function retrieveDraftSlips(string $bookId = null): array
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(Auth::id());
        }
        $accounts = $this->account->retrieveAccounts($bookId);
        $slipsList = $this->slip->retrieveDraftSlips($bookId);
        $slips = [];

        foreach ($slipsList as $slipItem) {
            $slips[$slipItem['slip_id']] = [
                'date'         => $slipItem['date'],
                'slip_outline' => $slipItem['slip_outline'],
                'slip_memo'    => $slipItem['slip_memo'],
                'items'        => [],
            ];
            $slipEntriesList = $this->slip->retrieveSlipEntriesBoundTo($slipItem['slip_id']);
            foreach ($slipEntriesList as $slipEntryItem) {
                $slips[$slipItem['slip_id']]['items'][$slipEntryItem['slip_entry_id']] = [
                    'debit'   => ['account_id' => $slipEntryItem['debit'], 'account_title' => $accounts[$slipEntryItem['debit']]['account_title']],
                    'credit'  => ['account_id' => $slipEntryItem['credit'], 'account_title' => $accounts[$slipEntryItem['credit']]['account_title']],
                    'amount'  => $slipEntryItem['amount'],
                    'client'  => $slipEntryItem['client'],
                    'outline' => $slipEntryItem['outline'],
                ];
            }
        }

        return $slips;
    }

    /**
     * Retrieve a slip.
     *
     * @param string $slipId
     *
     * @return array
     */
    public function retrieveSlip(string $slipId): array
    {
        $slips = [];
        $slip_head = $this->slip->retrieveSlip($slipId);
        if (!is_null($slip_head)) {
            $bookId = $slip_head['book_id'];
            $accounts = $this->account->retrieveAccounts($bookId);
            $slips[$slipId] = [
                'date'         => $slip_head['date'],
                'slip_outline' => $slip_head['slip_outline'],
                'slip_memo'    => $slip_head['slip_memo'],
                'items'        => [],
            ];
            $slipEntriesList = $this->slip->retrieveSlipEntriesBoundTo($slipId);
            foreach ($slipEntriesList as $slipEntryItem) {
                $slips[$slipId]['items'][$slipEntryItem['slip_entry_id']] = [
                    'debit'   => ['account_id' => $slipEntryItem['debit'], 'account_title' => $accounts[$slipEntryItem['debit']]['account_title']],
                    'credit'  => ['account_id' => $slipEntryItem['credit'], 'account_title' => $accounts[$slipEntryItem['credit']]['account_title']],
                    'amount'  => $slipEntryItem['amount'],
                    'client'  => $slipEntryItem['client'],
                    'outline' => $slipEntryItem['outline'],
                ];
            }
        }

        return $slips;
    }

    /**
     * Retrieve slips.
     *
     * @param string $fromDate
     * @param string $toDate
     * @param string $debit
     * @param string $credit
     * @param string $and_or
     * @param string $keyword
     * @param string $bookId
     *
     * @return array
     */
    public function retrieveSlips(?string $fromDate, ?string $toDate, ?string $debit, ?string $credit, ?string $and_or, ?string $keyword, string $bookId = null): array
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(Auth::id());
        }
        $accounts = $this->account->retrieveAccounts($bookId);
        if (empty($fromDate)) {
            $fromDate = self::ORIGIN_DATE;
        }
        if (empty($toDate)) {
            $date = new Carbon();
            $toDate = $date->format('Y-m-d');
        }
        $slipEntries = $this->slip->retrieveSlipEntries(
            $fromDate,
            $toDate,
            [
                'debit'    => $debit,
                'credit'   => $credit,
                'and_or'   => $and_or,
                'keyword'  => $keyword,
            ],
            $bookId
        );
        $slips = [];

        foreach ($slipEntries as $entry) {
            if (!array_key_exists($entry['slip_id'], $slips)) {  /* This is the first time that the entry which belongs to the slip appears. */
                $slips[$entry['slip_id']] = [
                    'date'         => $entry['date'],
                    'slip_outline' => $entry['slip_outline'],
                    'slip_memo'    => $entry['slip_memo'],
                    'items'        => [],
                ];
            }
            $slips[$entry['slip_id']]['items'][$entry['slip_entry_id']] = [
                'debit'   => ['account_id' => $entry['debit'], 'account_title' => $accounts[$entry['debit']]['account_title']],
                'credit'  => ['account_id' => $entry['credit'], 'account_title' => $accounts[$entry['credit']]['account_title']],
                'amount'  => $entry['amount'],
                'client'  => $entry['client'],
                'outline' => $entry['outline'],
            ];
        }

        return $slips;
    }

    /**
     * Retrieve amount changes between the specified date.
     *
     * @param string $fromDate
     * @param string $toDate
     * @param string $bookId
     *
     * @return array
     */
    public function retrieveStatements(string $fromDate, string $toDate, string $bookId = null): array
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(Auth::id());
        }
        $accounts = $this->account->retrieveAccounts($bookId);
        $amountFlows = $this->slip->retrieveAmountFlows($fromDate, $toDate, $bookId);
        $statements = [
            AccountService::ACCOUNT_TYPE_ASSET     => ['amount' => 0, 'groups' => []],
            AccountService::ACCOUNT_TYPE_LIABILITY => ['amount' => 0, 'groups' => []],
            AccountService::ACCOUNT_TYPE_EXPENSE   => ['amount' => 0, 'groups' => []],
            AccountService::ACCOUNT_TYPE_REVENUE   => ['amount' => 0, 'groups' => []],
            'current_net_asset'                    => ['amount' => 0],
            'net_income'                           => ['amount' => 0],
            'net_asset'                            => ['amount' => 0],
        ];

        foreach ($amountFlows as $accountId => $sumVaule) {
            if (($sumVaule['debit'] - $sumVaule['credit']) != 0) {
                $accountType = $accounts[$accountId]['account_type'];
                if (($accountType == AccountService::ACCOUNT_TYPE_ASSET) || ($accountType == AccountService::ACCOUNT_TYPE_EXPENSE)) {
                    $amount = $sumVaule['debit'] - $sumVaule['credit'];
                } else {
                    $amount = $sumVaule['credit'] - $sumVaule['debit'];
                }
                $accountGroupId = $accounts[$accountId]['account_group_id'];
                $statements[$accountType]['amount'] += $amount;
                if (!array_key_exists($accountGroupId, $statements[$accountType]['groups'])) {  /* This is the first time that the account which belongs to the group appears. */
                    $statements[$accountType]['groups'][$accountGroupId] = [
                        'title'     => $accounts[$accountId]['account_group_title'],
                        'isCurrent' => $accounts[$accountId]['is_current'],
                        'amount'    => 0,
                        'bk_code'   => $accounts[$accountId]['account_group_bk_code'],
                        'createdAt' => $accounts[$accountId]['account_group_created_at'],
                        'items'     => [],
                    ];
                }
                $statements[$accountType]['groups'][$accountGroupId]['amount'] += $amount;
                if ($accounts[$accountId]['is_current']) {
                    if ($accountType == AccountService::ACCOUNT_TYPE_ASSET) {
                        $statements['current_net_asset']['amount'] += $amount;
                    } elseif ($accountType == AccountService::ACCOUNT_TYPE_LIABILITY) {
                        $statements['current_net_asset']['amount'] -= $amount;
                    } else {
                    }
                }
                $statements[$accountType]['groups'][$accountGroupId]['items'][$accountId] = [
                    'title'     => $accounts[$accountId]['account_title'],
                    'amount'    => $amount,
                    'bk_code'   => $accounts[$accountId]['account_bk_code'],
                    'createdAt' => $accounts[$accountId]['created_at'],
                ];
            }
        }
        $statements['net_income']['amount'] = $statements[AccountService::ACCOUNT_TYPE_REVENUE]['amount'] - $statements[AccountService::ACCOUNT_TYPE_EXPENSE]['amount'];
        $statements['net_asset']['amount'] = $statements[AccountService::ACCOUNT_TYPE_ASSET]['amount'] - $statements[AccountService::ACCOUNT_TYPE_LIABILITY]['amount'];

        return $statements;
    }

    /**
     * Submit Slip with specified date.
     *
     * @param string $date
     * @param string $bookId
     *
     * @return void
     */
    public function submitDraftSlip(string $date, string $bookId = null)
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(Auth::id());
        }
        $draftSlips = $this->slip->retrieveDraftSlips($bookId);
        if (!empty($draftSlips)) {
            $this->slip->updateDate($draftSlips[0]['slip_id'], $date);
            $this->slip->submitSlip($draftSlips[0]['slip_id']);
        }
    }

    /**
     * Update Account.
     *
     * @param string $accountId
     * @param array  $newData
     * @param string $bookId
     */
    public function updateAccount(string $accountId, array $newData, string $bookId)
    {
        $this->account->updateAccount($accountId, $newData);
    }

    /**
     * Update Account Group.
     *
     * @param string $accountGroupId
     * @param array  $newData
     * @param string $bookId
     */
    public function updateAccountGroup(string $accountGroupId, array $newData, string $bookId)
    {
        $this->account->updateAccountGroup($accountGroupId, $newData);
    }

    /**
     * Validate date format.
     *
     * @param string $date
     *
     * @return bool
     */
    public function validateDateFormat(string $date): bool
    {
        $success = false;

        if (strptime($date, '%Y-%m-%d')) {
            $d = Carbon::createFromFormat('Y-m-d', $date);
            if ($d) {
                if ($d->format('Y-m-d') == $date) {
                    $success = true;
                }
            }
        }

        return $success;
    }

    /**
     * Validate period.
     *
     * @param string $fromDate
     * @param string $toDate
     *
     * @return bool
     */
    public function validatePeriod(string $fromDate, string $toDate): bool
    {
        $success = true;

        if (empty($fromDate)) {
            $fromDate = self::ORIGIN_DATE;
        } else {
            if (!$this->validateDateFormat($fromDate)) {
                $success = false;
            }
        }
        if (empty($toDate)) {
            $date = new Carbon();
            $toDate = $date->format('Y-m-d');
        } else {
            if (!$this->validateDateFormat($toDate)) {
                $success = false;
            }
        }
        if ($success) {
            $fromDateObj = new Carbon($fromDate);
            $tomDateObj = new Carbon($toDate);
            if ($fromDateObj->gt($tomDateObj)) {
                $success = false;
            }
        }

        return $success;
    }
}
