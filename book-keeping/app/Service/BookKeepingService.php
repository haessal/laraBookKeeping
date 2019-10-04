<?php

namespace App\Service;

use Illuminate\Support\Facades\Auth;

class BookKeepingService
{
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
     * Retrieve amount changes between the specified period.
     *
     * @param string $datfromDate
     * @param string $toDate
     * @param string $bookId
     *
     * @return array
     */
    public function retrieveStatements(string $fromDate, string $toDate, string $bookId = null) : array
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
            if ($sumVaule['debit'] != $sumVaule['credit']) {
                $accountType = $accounts[$accountId]['account_type'];
                if (($accountType == AccountService::ACCOUNT_TYPE_ASSET) || ($accountType == AccountService::ACCOUNT_TYPE_EXPENSE)) {
                    $amount = $sumVaule['debit'] - $sumVaule['credit'];
                } else {
                    $amount = $sumVaule['credit'] - $sumVaule['debit'];
                }
                $accountGroupId = $accounts[$accountId]['account_group_id'];
                $statements[$accountType]['amount'] += $amount;
                if (!array_key_exists($accountGroupId, $statements[$accountType]['groups'])) {
                    $statements[$accountType]['groups'][$accountGroupId] = [
                        'title'     => $accounts[$accountId]['account_group_title'],
                        'isCurrent' => $accounts[$accountId]['is_current'],
                        'amount'    => 0,
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
                        /* NOP */
                    }
                }
                $statements[$accountType]['groups'][$accountGroupId]['items'][$accountId] = [
                    'title' => $accounts[$accountId]['account_title'], 'amount' => $amount,
                ];
            }
        }
        $statements['net_income']['amount'] = $statements[AccountService::ACCOUNT_TYPE_REVENUE]['amount'] - $statements[AccountService::ACCOUNT_TYPE_EXPENSE]['amount'];
        $statements['net_asset']['amount'] = $statements[AccountService::ACCOUNT_TYPE_ASSET]['amount'] - $statements[AccountService::ACCOUNT_TYPE_LIABILITY]['amount'];

        return $statements;
    }
}
