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
     * The status code of the service.
     *
     * @var int
     */
    const STATUS_NORMAL = 0;
    const STATUS_ERROR_AUTH_NOTAVAILABLE = 1;
    const STATUS_ERROR_AUTH_FORBIDDEN = 2;
    const STATUS_ERROR_BAD_CONDITION = 3;

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
     * @param  \App\Service\BookService  $book
     * @param  \App\Service\AccountService  $account
     * @param  \App\Service\BudgetService  $budget
     * @param  \App\Service\SlipService  $slip
     */
    public function __construct(BookService $book, AccountService $account, BudgetService $budget, SlipService $slip)
    {
        $this->book = $book;
        $this->account = $account;
        $this->budget = $budget;
        $this->slip = $slip;
    }

    /**
     * Authorize the user to access the book.
     *
     * @param  string  $bookId
     * @param  string  $userName
     * @param  'ReadWrite'|'ReadOnly'  $mode
     * @return array{0:int, 1:array{user: string, permitted_to: 'ReadWrite'|'ReadOnly'}|null}
     */
    public function authorizeToAccess($bookId, $userName, $mode): array
    {
        [$authorized, $reason] = $this->isOwner($bookId);
        if (! $authorized) {
            return [$reason, null];
        }
        $status = self::STATUS_NORMAL;
        $permissionList = $this->book->retrievePermissions($bookId);
        foreach ($permissionList as $permissionItem) {
            if ($permissionItem['user'] == $userName) {
                if ($permissionItem['permitted_to'] != $mode) {
                    $status = self::STATUS_ERROR_BAD_CONDITION;
                }

                return [$status, null];
            }
        }
        $bookPermission = $this->book->createPermission($bookId, $userName, $mode);

        return [$status, $bookPermission];
    }

    /**
     * Create a new account.
     *
     * @param  string  $accountGroupId
     * @param  string  $title
     * @param  string  $description
     * @param  string  $bookId
     * @return string
     */
    public function createAccount($accountGroupId, $title, $description, $bookId)
    {
        $accountId = $this->account->createAccount($accountGroupId, $title, $description);

        return $accountId;
    }

    /**
     * Create a new account group.
     *
     * @param  string  $accountType
     * @param  string  $title
     * @param  string  $bookId
     * @return string
     */
    public function createAccountGroup($accountType, $title, $bookId)
    {
        $accountGroupId = $this->account->createAccountGroup($bookId, $accountType, $title);

        return $accountGroupId;
    }

    /**
     * Create a new book.
     *
     * @param  string  $title
     * @return string $bookId
     */
    public function createBook($title)
    {
        $bookId = $this->book->createBook(intval(Auth::id()), $title);

        return $bookId;
    }

    /**
     * Create a new slip.
     *
     * @param  string  $outline
     * @param  string  $date
     * @param  array{
     *   debit: string,
     *   credit: string,
     *   amount: int,
     *   client: string,
     *   outline: string,
     *   display_order?: string,
     * }[]  $entries
     * @param  string|null  $memo
     * @param  string|null  $bookId
     * @return string
     */
    public function createSlip($outline, $date, array $entries, $memo, $bookId = null)
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(intval(Auth::id()));
        }
        $bookId = strval($bookId); // FIXME: in case default book is not setting
        $slipId = $this->slip->createSlipAsDraft($bookId, $outline, $date, $entries, $memo);
        $this->slip->submitSlip($slipId);

        return $slipId;
    }

    /**
     * Create a new slip entry and add it to the draft slip.
     *
     * @param  string  $debit
     * @param  string  $client
     * @param  string  $outline
     * @param  string  $credit
     * @param  int  $amount
     * @param  string|null  $bookId
     * @return void
     */
    public function createSlipEntryAsDraft($debit, $client, $outline, $credit, $amount, $bookId = null)
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(intval(Auth::id()));
        }
        $bookId = strval($bookId); // FIXME: in case default book is not setting
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
     * Delete the slip entry and the slip that no longer have a entry.
     *
     * @param  string  $slipEntryId
     * @param  string|null  $bookId
     * @return void
     */
    public function deleteSlipEntryAndEmptySlip($slipEntryId, $bookId = null)
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(intval(Auth::id()));
        }
        $bookId = strval($bookId); // FIXME: in case default book is not setting
        $slipEntry = $this->slip->retrieveSlipEntry($slipEntryId, $bookId, true);
        if (isset($slipEntry)) {
            $slipId = $slipEntry['slip_id'];
            $this->slip->deleteSlipEntry($slipEntryId);
            $slipEntries = $this->slip->retrieveSlipEntriesBoundTo($slipId);
            if (empty($slipEntries)) {
                $this->slip->deleteSlip($slipId);
            }
        }
    }

    /**
     * Forbid the user to access the book.
     *
     * @param  string  $bookId
     * @param  string  $userName
     * @return array{0:int, 1:array{user: string, permitted_to: 'ReadWrite'|'ReadOnly'}|null}
     */
    public function forbidToAccess($bookId, $userName): array
    {
        [$authorized, $reason] = $this->isOwner($bookId);
        if (! $authorized) {
            return [$reason, null];
        }
        $owner = $this->book->retrieveOwnerNameOf($bookId);
        if (isset($owner) && ($userName == strval($owner))) {
            return [self::STATUS_ERROR_BAD_CONDITION, null];
        }
        $bookPermission = null;
        $permissionList = $this->book->retrievePermissions($bookId);
        foreach ($permissionList as $permissionItem) {
            if ($permissionItem['user'] == $userName) {
                $bookPermission = $permissionItem;
                $this->book->deletePermission($bookId, $userName);
                break;
            }
        }

        return [self::STATUS_NORMAL, $bookPermission];
    }

    /**
     * Retrieve a list of accounts.
     *
     * @param  string|null  $bookId
     * @return array<string, array{
     *   account_type: string,
     *   account_group_id: string,
     *   account_group_title: string,
     *   is_current: bool,
     *   account_id: string,
     *   account_title: string,
     *   description: string,
     *   selectable: bool,
     *   account_bk_code: int,
     *   created_at: string,
     *   account_group_bk_code: int,
     *   account_group_created_at: string,
     * }>
     */
    public function retrieveAccounts($bookId = null): array
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(intval(Auth::id()));
        }
        $bookId = strval($bookId); // FIXME: in case default book is not setting
        $accounts = $this->account->retrieveAccounts($bookId);

        return $accounts;
    }

    /**
     * Retrieve a list of available books.
     *
     * @return array{
     *   id: string,
     *   name: string,
     *   is_default: bool,
     *   is_owner: bool,
     *   modifiable: bool,
     *   owner: string,
     * }[]
     */
    public function retrieveAvailableBooks(): array
    {
        $books = [];

        $bookList = $this->book->retrieveBooks(intval(Auth::id()));
        foreach ($bookList as $book) {
            $id = $book['book_id'];
            $owner = $this->book->retrieveOwnerNameOf($id);
            $books[] = [
                'id'         => $id,
                'name'       => $book['book_name'],
                'is_default' => $book['is_default'],
                'is_owner'   => $book['is_owner'],
                'modifiable' => $book['modifiable'],
                'owner'      => strval($owner),
            ];
        }

        return $books;
    }

    /**
     * Retrieve the book if it is available.
     *
     * @param  string  $bookId
     * @return array{
     *   id: string,
     *   name: string,
     *   is_default: bool,
     *   is_owner: bool,
     *   modifiable: bool,
     *   owner: string,
     * }|null
     */
    public function retrieveBook($bookId): ?array
    {
        $book = null;

        $bookItem = $this->book->retrieveBook($bookId, intval(Auth::id()));
        if (isset($bookItem)) {
            $owner = $this->book->retrieveOwnerNameOf($bookId);
            $book = [
                'id'         => $bookId,
                'name'       => $bookItem['book_name'],
                'is_default' => $bookItem['is_default'],
                'is_owner'   => $bookItem['is_owner'],
                'modifiable' => $bookItem['modifiable'],
                'owner'      => strval($owner),
            ];
        }

        return $book;
    }

    /**
     * Retrieve the information of the book.
     *
     * @param  string  $bookId
     * @return array{
     *   id: string,
     *   owner: string,
     *   name: string,
     * }|null
     */
    public function retrieveBookInformation($bookId): ?array
    {
        $information = null;

        $owner = $this->book->retrieveOwnerNameOf($bookId);
        $book = $this->book->retrieveInformationOf($bookId);
        if (isset($owner) && isset($book)) {
            $information = ['id' => $bookId, 'owner' => $owner, 'name' => $book['book_name']];
        }

        return $information;
    }

    /**
     * Retrieve a list of accounts categorized into groups.
     *
     * @param  bool  $selectableOnly
     * @param  string|null  $bookId
     * @return array<string, array{
     *   groups:array<string, array{
     *     title: string,
     *     isCurrent: bool,
     *     bk_code: int,
     *     createdAt: string,
     *     items: array<string, array{
     *       title: string,
     *       description: string,
     *       selectable: bool,
     *       bk_code: int,
     *       createdAt: string,
     *     }>
     *   }>|array{}
     * }>
     */
    public function retrieveCategorizedAccounts($selectableOnly = false, $bookId = null): array
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(intval(Auth::id()));
        }
        $bookId = strval($bookId); // FIXME: in case default book is not setting
        $accounts = $this->account->retrieveAccounts($bookId);
        $accountGroups = $this->account->retrieveAccountGroups($bookId);

        $accounts_menu = [
            AccountService::ACCOUNT_TYPE_ASSET     => ['groups' => []],
            AccountService::ACCOUNT_TYPE_LIABILITY => ['groups' => []],
            AccountService::ACCOUNT_TYPE_EXPENSE   => ['groups' => []],
            AccountService::ACCOUNT_TYPE_REVENUE   => ['groups' => []],
        ];

        foreach ($accounts as $accountsKey => $accountsItem) {
            if ((! $selectableOnly) || ($accountsItem['selectable'] == true)) {
                if (! array_key_exists($accountsItem['account_group_id'], $accounts_menu[$accountsItem['account_type']]['groups'])) {
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
        foreach ($accountGroups as $accountGroupsKey => $accountGroup) {
            if (! array_key_exists($accountGroupsKey, $accounts_menu[$accountGroup['account_type']]['groups'])) {
                $accounts_menu[$accountGroup['account_type']]['groups'][$accountGroupsKey] = [
                    'title'        => $accountGroup['account_group_title'],
                    'isCurrent'    => $accountGroup['is_current'],
                    'bk_code'      => $accountGroup['account_group_bk_code'],
                    'createdAt'    => $accountGroup['created_at'],
                    'items'        => [],
                ];
            }
        }

        return $accounts_menu;
    }

    /**
     * Retrieve the default book.
     *
     * @return array{0:int, 1:array{
     *   id: string,
     *   name: string,
     *   is_default: bool,
     *   is_owner: bool,
     *   modifiable: bool,
     *   owner: string,
     * }|null}
     */
    public function retrieveDefaultBook(): array
    {
        $bookId = $this->book->retrieveDefaultBook(intval(Auth::id()));
        if (isset($bookId)) {
            $book = $this->retrieveBook($bookId);

            return [self::STATUS_NORMAL, $book];
        } else {
            return [self::STATUS_ERROR_AUTH_NOTAVAILABLE, null];
        }
    }

    /**
     * Retrieve a list of draft slips with their entries.
     *
     * @param  string|null  $bookId
     * @return array<string, array{
     *   date: string,
     *   slip_outline: string,
     *   slip_memo: string,
     *   items: array<string, array{
     *     debit: array{account_id: string, account_title: string},
     *     credit: array{account_id: string, account_title: string},
     *     amount: int,
     *     client: string,
     *     outline: string,
     *   }>
     * }>|array{}
     */
    public function retrieveDraftSlips($bookId = null): array
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(intval(Auth::id()));
        }
        $bookId = strval($bookId); // FIXME: in case default book is not setting
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
                    'debit'   => [
                        'account_id'    => $slipEntryItem['debit'],
                        'account_title' => $accounts[$slipEntryItem['debit']]['account_title'],
                    ],
                    'credit'  => [
                        'account_id'    => $slipEntryItem['credit'],
                        'account_title' => $accounts[$slipEntryItem['credit']]['account_title'],
                    ],
                    'amount'  => $slipEntryItem['amount'],
                    'client'  => $slipEntryItem['client'],
                    'outline' => $slipEntryItem['outline'],
                ];
            }
        }

        return $slips;
    }

    /**
     * Retrieve a list of users who can access to the book.
     *
     * @param  string  $bookId
     * @return array{0:int, 1:array{user: string, permitted_to: 'ReadWrite'|'ReadOnly'}[]}
     */
    public function retrievePermittedUsers($bookId): array
    {
        [$authorized, $reason] = $this->isOwner($bookId);
        if (! $authorized) {
            return [$reason, []];
        }
        $status = self::STATUS_NORMAL;
        $permissionList = $this->book->retrievePermissions($bookId);

        return [$status, $permissionList];
    }

    /**
     * Retrieve the slip.
     *
     * @param  string  $slipId
     * @param  string|null  $bookId
     * @return array<string, array{
     *   date: string,
     *   slip_outline: string,
     *   slip_memo: string,
     *   items: array<string, array{
     *     debit: array{account_id: string, account_title: string},
     *     credit: array{account_id: string, account_title: string},
     *     amount: int,
     *     client: string,
     *     outline: string,
     *   }>
     * }>|array{}
     */
    public function retrieveSlip($slipId, $bookId = null): array
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(intval(Auth::id()));
        }
        $bookId = strval($bookId); // FIXME: in case default book is not setting
        $slips = [];
        $slip_head = $this->slip->retrieveSlip($slipId, $bookId);
        if (isset($slip_head)) {
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
                    'debit'   => [
                        'account_id'    => $slipEntryItem['debit'],
                        'account_title' => $accounts[$slipEntryItem['debit']]['account_title'],
                    ],
                    'credit'  => [
                        'account_id'    => $slipEntryItem['credit'],
                        'account_title' => $accounts[$slipEntryItem['credit']]['account_title'],
                    ],
                    'amount'  => $slipEntryItem['amount'],
                    'client'  => $slipEntryItem['client'],
                    'outline' => $slipEntryItem['outline'],
                ];
            }
        }

        return $slips;
    }

    /**
     * Retrieve the slip entry.
     *
     * @param  string  $slipEntryId
     * @param  string|null  $bookId
     * @return array<string, array{
     *   date: string,
     *   slip_outline: string,
     *   slip_memo: string,
     *   items: array<string, array{
     *     debit: array{account_id: string, account_title: string},
     *     credit: array{account_id: string, account_title: string},
     *     amount: int,
     *     client: string,
     *     outline: string,
     *   }>
     * }>|array{}
     */
    public function retrieveSlipEntry($slipEntryId, $bookId = null): array
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(intval(Auth::id()));
        }
        $bookId = strval($bookId); // FIXME: in case default book is not setting
        $accounts = $this->account->retrieveAccounts($bookId);
        $slipEntry = $this->slip->retrieveSlipEntry($slipEntryId, $bookId, false);
        $slips = [];
        if (isset($slipEntry)) {
            $slips[$slipEntry['slip_id']] = [
                'date'         => $slipEntry['date'],
                'slip_outline' => $slipEntry['slip_outline'],
                'slip_memo'    => $slipEntry['slip_memo'],
                'items'        => [
                    $slipEntry['slip_entry_id'] => [
                        'debit'   => [
                            'account_id'    => $slipEntry['debit'],
                            'account_title' => $accounts[$slipEntry['debit']]['account_title'],
                        ],
                        'credit'  => [
                            'account_id'    => $slipEntry['credit'],
                            'account_title' => $accounts[$slipEntry['credit']]['account_title'],
                        ],
                        'amount'  => $slipEntry['amount'],
                        'client'  => $slipEntry['client'],
                        'outline' => $slipEntry['outline'],
                    ],
                ],
            ];
        }

        return $slips;
    }

    /**
     * Retrieve a list of slips with their entries.
     *
     * @param  string|null  $fromDate
     * @param  string|null  $toDate
     * @param  string|null  $debit
     * @param  string|null  $credit
     * @param  string|null  $and_or
     * @param  string|null  $keyword
     * @param  string|null  $bookId
     * @return array<string, array{
     *   date: string,
     *   slip_outline: string,
     *   slip_memo: string,
     *   items: array<string, array{
     *     debit: array{account_id: string, account_title: string},
     *     credit: array{account_id: string, account_title: string},
     *     amount: int,
     *     client: string,
     *     outline: string,
     *   }>
     * }>|array{}
     */
    public function retrieveSlips($fromDate, $toDate, $debit, $credit, $and_or, $keyword, $bookId = null): array
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(intval(Auth::id()));
        }
        $bookId = strval($bookId); // FIXME: in case default book is not setting
        $accounts = $this->account->retrieveAccounts($bookId);
        if (is_null($fromDate)) {
            $fromDate = self::ORIGIN_DATE;
        }
        if (is_null($toDate)) {
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
            if (! array_key_exists($entry['slip_id'], $slips)) {  /* This is the first time that the entry which belongs to the slip appears. */
                $slips[$entry['slip_id']] = [
                    'date'         => $entry['date'],
                    'slip_outline' => $entry['slip_outline'],
                    'slip_memo'    => $entry['slip_memo'],
                    'items'        => [],
                ];
            }
            $slips[$entry['slip_id']]['items'][$entry['slip_entry_id']] = [
                'debit'   => [
                    'account_id'    => $entry['debit'],
                    'account_title' => $accounts[$entry['debit']]['account_title'],
                ],
                'credit'  => [
                    'account_id'    => $entry['credit'],
                    'account_title' => $accounts[$entry['credit']]['account_title'],
                ],
                'amount'  => $entry['amount'],
                'client'  => $entry['client'],
                'outline' => $entry['outline'],
            ];
        }

        return $slips;
    }

    /**
     * Retrieve the statement over the specified period.
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  string|null  $bookId
     * @return array{
     *   asset: array{
     *     amount: int,
     *     groups: array<string, array{
     *       title: string,
     *       isCurrent: bool,
     *       amount: int,
     *       bk_code: int,
     *       createdAt: string,
     *       items: array<string, array{
     *         title: string,
     *         amount: int,
     *         bk_code: int,
     *         createdAt: string,
     *       }>
     *     }>|array{}
     *   },
     *   liability: array{
     *     amount: int,
     *     groups: array<string, array{
     *       title: string,
     *       isCurrent: bool,
     *       amount: int,
     *       bk_code: int,
     *       createdAt: string,
     *       items: array<string, array{
     *         title: string,
     *         amount: int,
     *         bk_code: int,
     *         createdAt: string,
     *       }>
     *     }>|array{}
     *   },
     *   expense: array{
     *     amount: int,
     *     groups: array<string, array{
     *       title: string,
     *       isCurrent: bool,
     *       amount: int,
     *       bk_code: int,
     *       createdAt: string,
     *       items: array<string, array{
     *         title: string,
     *         amount: int,
     *         bk_code: int,
     *         createdAt: string,
     *       }>
     *     }>|array{}
     *   },
     *   revenue: array{
     *     amount: int,
     *     groups: array<string, array{
     *       title: string,
     *       isCurrent: bool,
     *       amount: int,
     *       bk_code: int,
     *       createdAt: string,
     *       items: array<string, array{
     *         title: string,
     *         amount: int,
     *         bk_code: int,
     *         createdAt: string,
     *       }>
     *     }>|array{}
     *   },
     *   current_net_asset: array{amount: int},
     *   net_income: array{amount: int},
     *   net_asset: array{amount: int},
     * }
     */
    public function retrieveStatement($fromDate, $toDate, $bookId = null): array
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(intval(Auth::id()));
        }
        $bookId = strval($bookId); // FIXME: in case default book is not setting
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

        foreach ($amountFlows as $accountId => $sumValue) {
            if (($sumValue['debit'] - $sumValue['credit']) != 0) {
                /** @var 'asset'|'liability'|'expense'|'revenue' $accountType */
                $accountType = $accounts[$accountId]['account_type'];
                if (($accountType == AccountService::ACCOUNT_TYPE_ASSET) || ($accountType == AccountService::ACCOUNT_TYPE_EXPENSE)) {
                    $amount = $sumValue['debit'] - $sumValue['credit'];
                } else {
                    $amount = $sumValue['credit'] - $sumValue['debit'];
                }
                $accountGroupId = $accounts[$accountId]['account_group_id'];
                $statements[$accountType]['amount'] += $amount;
                if (! array_key_exists($accountGroupId, $statements[$accountType]['groups'])) {  /* This is the first time that the account which belongs to the group appears. */
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
     * Set the book as the default one.
     *
     * @param  string  $bookId
     * @return array{0:int, 1:array{
     *   id: string,
     *   name: string,
     *   is_default: bool,
     *   is_owner: bool,
     *   modifiable: bool,
     *   owner: string,
     * }|null}
     */
    public function setBookAsDefault($bookId): array
    {
        [$authorized, $reason] = $this->isOwner($bookId);
        if (! $authorized) {
            return [$reason, null];
        }
        $previous_default = $this->book->retrieveDefaultBook(intval(Auth::id()));
        if (is_null($previous_default) || ($previous_default == $bookId)) {
            $this->book->updateDefaultMarkOf($bookId, intval(Auth::id()), true);

            return [self::STATUS_NORMAL, $this->retrieveBook($bookId)];
        } else {
            return [self::STATUS_ERROR_BAD_CONDITION, null];
        }
    }

    /**
     * Submit the draft slip.
     *
     * @param  string  $date
     * @param  string|null  $bookId
     * @return void
     */
    public function submitDraftSlip($date, $bookId = null)
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(intval(Auth::id()));
        }
        $bookId = strval($bookId); // FIXME: in case default book is not setting
        $draftSlips = $this->slip->retrieveDraftSlips($bookId);
        if (! empty($draftSlips)) {
            $this->slip->updateDateOf($draftSlips[0]['slip_id'], $date);
            $this->slip->submitSlip($draftSlips[0]['slip_id']);
        }
    }

    /**
     * Unset the book as the default one.
     *
     * @param  string  $bookId
     * @return array{0:int, 1:array{
     *   id: string,
     *   name: string,
     *   is_default: bool,
     *   is_owner: bool,
     *   modifiable: bool,
     *   owner: string,
     * }|null}
     */
    public function unsetBookAsDefault($bookId): array
    {
        [$authorized, $reason] = $this->isOwner($bookId);
        if (! $authorized) {
            return [$reason, null];
        }
        $this->book->updateDefaultMarkOf($bookId, intval(Auth::id()), false);
        $book = $this->retrieveBook($bookId);

        return [self::STATUS_NORMAL, $book];
    }

    /**
     * Update the account.
     *
     * @param  string  $accountId
     * @param  array{
     *   group?: string,
     *   title?: string,
     *   description?: string,
     *   selectable?: bool,
     * }  $newData
     * @param  string  $bookId
     * @return void
     */
    public function updateAccount($accountId, array $newData, $bookId)
    {
        $this->account->updateAccount($accountId, $newData);
    }

    /**
     * Update the account group.
     *
     * @param  string  $accountGroupId
     * @param  array{
     *   title?: string,
     *   is_current?: bool,
     * }  $newData
     * @param  string  $bookId
     * @return void
     */
    public function updateAccountGroup($accountGroupId, array $newData, $bookId)
    {
        $this->account->updateAccountGroup($accountGroupId, $newData);
    }

    /**
     * Rename the book.
     *
     * @param  string  $bookId
     * @param  string  $newName
     * @return array{0:int, 1:null}
     */
    public function updateBookName($bookId, $newName): array
    {
        [$authorized, $reason] = $this->isOwner($bookId);
        if (! $authorized) {
            return [$reason, null];
        }
        $this->book->updateNameOf($bookId, $newName);

        return [self::STATUS_NORMAL, null];
    }

    /**
     * Update the slip.
     *
     * @param  string  $slipId
     * @param  array{
     *   outline?: string,
     *   memo?: string,
     *   date?: string,
     * }  $newData
     * @param  string|null  $bookId
     * @return void
     */
    public function updateSlip($slipId, array $newData, $bookId = null)
    {
        $this->slip->updateSlip($slipId, $newData);
    }

    /**
     * Update the slip entry.
     *
     * @param  string  $slipEntryId
     * @param  array{
     *   debit?: string,
     *   credit?: string,
     *   amount?: int,
     *   client?: string,
     *   outline?: string,
     * }  $newData
     * @param  string|null  $bookId
     * @return void
     */
    public function updateSlipEntry($slipEntryId, array $newData, $bookId = null)
    {
        $this->slip->updateSlipEntry($slipEntryId, $newData);
    }

    /**
     * Check if the date is in valid format.
     *
     * @param  string  $date
     * @return bool
     */
    public function validateDateFormat($date)
    {
        $success = false;

        $parse_result = date_parse_from_format('Y-m-d', $date);
        if ($parse_result['error_count'] == 0) {
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
     * Check if the period is valid.
     *
     * @param  string|null  $fromDate
     * @param  string|null  $toDate
     * @return bool
     */
    public function validatePeriod($fromDate, $toDate)
    {
        $success = true;

        if (is_null($fromDate) || ($fromDate == '')) {
            $fromDate = self::ORIGIN_DATE;
        } else {
            if (! $this->validateDateFormat($fromDate)) {
                $success = false;
            }
        }
        if (is_null($toDate) || ($toDate == '')) {
            $date = new Carbon();
            $toDate = $date->format('Y-m-d');
        } else {
            if (! $this->validateDateFormat($toDate)) {
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

    /**
     * Check if the UUID is in valid format.
     *
     * @param  string  $uuid
     * @return bool
     */
    public function validateUuid($uuid)
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) === 1;
    }

    /**
     * Check if the authenticated user is the owner of the book.
     *
     * @param  string  $bookId
     * @return array{0:bool, 1:int}
     */
    private function isOwner($bookId): array
    {
        $bookItem = $this->book->retrieveBook($bookId, intval(Auth::id()));
        if (is_null($bookItem)) {
            return [false, self::STATUS_ERROR_AUTH_NOTAVAILABLE];
        }
        if ($bookItem['is_owner'] == false) {
            return [false, self::STATUS_ERROR_AUTH_FORBIDDEN];
        }

        return [true, self::STATUS_NORMAL];
    }
}
