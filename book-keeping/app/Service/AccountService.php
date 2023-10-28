<?php

namespace App\Service;

use App\DataProvider\AccountGroupRepositoryInterface;
use App\DataProvider\AccountRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class AccountService
{
    /**
     * The key of Asset in the "account_type" column.
     *
     * @var string
     */
    const ACCOUNT_TYPE_ASSET = 'asset';

    /**
     * The key of Liability in the "account_type" column.
     *
     * @var string
     */
    const ACCOUNT_TYPE_LIABILITY = 'liability';

    /**
     * The key of Expense in the "account_type" column.
     *
     * @var string
     */
    const ACCOUNT_TYPE_EXPENSE = 'expense';

    /**
     * The key of Revenue in the "account_type" column.
     *
     * @var string
     */
    const ACCOUNT_TYPE_REVENUE = 'revenue';

    /**
     * Account repository instance.
     *
     * @var \App\DataProvider\AccountRepositoryInterface
     */
    private $account;

    /**
     * Account group repository instance.
     *
     * @var \App\DataProvider\AccountGroupRepositoryInterface
     */
    private $accountGroup;

    /**
     * Create a new AccountService instance.
     *
     * @param  \App\DataProvider\AccountRepositoryInterface  $account
     * @param  \App\DataProvider\AccountGroupRepositoryInterface  $accountGroup
     */
    public function __construct(AccountRepositoryInterface $account, AccountGroupRepositoryInterface $accountGroup)
    {
        $this->account = $account;
        $this->accountGroup = $accountGroup;
    }

    /**
     * Create a new account.
     *
     * @param  string  $accountGroupId
     * @param  string  $title
     * @param  string  $description
     * @param  int  $bk_uid
     * @param  int  $bk_code
     * @return string $accountId
     */
    public function createAccount($accountGroupId, $title, $description, $bk_uid = null, $bk_code = null)
    {
        $accountId = $this->account->create($accountGroupId, $title, $description, $bk_uid, $bk_code);

        return $accountId;
    }

    /**
     * Create a new account group.
     *
     * @param  string  $bookId
     * @param  string  $accountType
     * @param  string  $title
     * @param  bool  $isCurrent
     * @param  int  $bk_uid
     * @param  int  $bk_code
     * @return string $accountGroupId
     */
    public function createAccountGroup($bookId, $accountType, $title, $isCurrent = false, $bk_uid = null, $bk_code = null)
    {
        $accountGroupId = $this->accountGroup->create($bookId, $accountType, $title, $isCurrent, $bk_uid, $bk_code);

        return $accountGroupId;
    }

    /**
     * Dump accounts of the book.
     *
     * @param  string  $bookId
     * @return array<string, array{
     *   account_group_id: string,
     *   book_id: string,
     *   account_type: string,
     *   account_group_title: string,
     *   bk_uid: int|null,
     *   account_group_bk_code: int|null,
     *   is_current: bool,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     *   items: array<string, array{
     *     account_id: string,
     *     account_group_id: string,
     *     account_title: string,
     *     description: string,
     *     selectable: bool,
     *     bk_uid: int|null,
     *     account_bk_code: int|null,
     *     display_order: int|null,
     *     updated_at: string|null,
     *     deleted: bool,
     *   }>,
     * }>
     */
    public function dumpAccounts($bookId): array
    {
        $accountGroups = [];

        /** @var array{
         *   account_group_id: string,
         *   book_id: string,
         *   account_type: string,
         *   account_group_title: string,
         *   bk_uid: int|null,
         *   account_group_bk_code: int|null,
         *   is_current: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $accountGroupList
         */
        $accountGroupList = $this->accountGroup->searchBookForExporting($bookId);
        foreach ($accountGroupList as $accountGroup) {
            $accountGroupId = $accountGroup['account_group_id'];
            /** @var array{
             *   account_group_id: string,
             *   book_id: string,
             *   account_type: string,
             *   account_group_title: string,
             *   bk_uid: int|null,
             *   account_group_bk_code: int|null,
             *   is_current: bool,
             *   display_order: int|null,
             *   updated_at: string|null,
             *   deleted: bool,
             * } $convertedAccountGroup
             */
            $convertedAccountGroup = $this->convertExportedTimestamps($accountGroup);
            $accountGroups[$accountGroupId] = $convertedAccountGroup;
            $accountItems = [];
            /** @var array{
             *   account_id: string,
             *   account_group_id: string,
             *   account_title: string,
             *   description: string,
             *   selectable: bool,
             *   bk_uid: int|null,
             *   account_bk_code: int|null,
             *   display_order: int|null,
             *   created_at: string|null,
             *   updated_at: string|null,
             *   deleted_at: string|null,
             * }[] $accountItemList
             */
            $accountItemList = $this->account->searchAccountGropupForExporting($accountGroupId);
            foreach ($accountItemList as $accountItem) {
                /** @var array{
                 *   account_id: string,
                 *   account_group_id: string,
                 *   account_title: string,
                 *   description: string,
                 *   selectable: bool,
                 *   bk_uid: int|null,
                 *   account_bk_code: int|null,
                 *   display_order: int|null,
                 *   updated_at: string|null,
                 *   deleted: bool,
                 * } $convertedAccountItem
                 */
                $convertedAccountItem = $this->convertExportedTimestamps($accountItem);
                $accountItems[$accountItem['account_id']] = $convertedAccountItem;
            }
            $accountGroups[$accountGroupId]['items'] = $accountItems;
        }

        return $accountGroups;
    }

    /**
     * Export the account group.
     *
     * @param  string  $bookId
     * @param  string  $accountGroupId
     * @return array<string, array{
     *   account_group_id: string,
     *   book_id: string,
     *   account_type: string,
     *   account_group_title: string,
     *   bk_uid: int|null,
     *   account_group_bk_code: int|null,
     *   is_current: bool,
     *   display_order: int|null,
     *   updated_at: string|null,
     *   deleted: bool,
     * }>
     */
    public function exportAccountGroup($bookId, $accountGroupId): array
    {
        $accountGroups = [];

        /** @var array{
         *   account_group_id: string,
         *   book_id: string,
         *   account_type: string,
         *   account_group_title: string,
         *   bk_uid: int|null,
         *   account_group_bk_code: int|null,
         *   is_current: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $accountGroupList
         */
        $accountGroupList = $this->accountGroup->searchBookForExporting($bookId, $accountGroupId);
        foreach ($accountGroupList as $accountGroup) {
            /** @var array{
             *   account_group_id: string,
             *   book_id: string,
             *   account_type: string,
             *   account_group_title: string,
             *   bk_uid: int|null,
             *   account_group_bk_code: int|null,
             *   is_current: bool,
             *   display_order: int|null,
             *   updated_at: string|null,
             *   deleted: bool,
             * } $convertedAccountGroup
             */
            $convertedAccountGroup = $this->convertExportedTimestamps($accountGroup);
            $accountGroups[$accountGroup['account_group_id']] = $convertedAccountGroup;
        }

        return $accountGroups;
    }

    /**
     * Export the account item.
     *
     * @param  string  $bookId
     * @param  string  $accountGroupId
     * @param  string  $accountId
     * @return array<string, array{
     *   items: array<string, array{
     *     account_id: string,
     *     account_group_id: string,
     *     account_title: string,
     *     description: string,
     *     selectable: bool,
     *     bk_uid: int|null,
     *     account_bk_code: int|null,
     *     display_order: int|null,
     *     updated_at: string|null,
     *     deleted: bool,
     *   }>,
     * }>
     */
    public function exportAccountItem($bookId, $accountGroupId, $accountId): array
    {
        $accountGroups = [];

        /** @var array{
         *   account_group_id: string,
         *   book_id: string,
         *   account_type: string,
         *   account_group_title: string,
         *   bk_uid: int|null,
         *   account_group_bk_code: int|null,
         *   is_current: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $accountGroupList
         */
        $accountGroupList = $this->accountGroup->searchBookForExporting($bookId, $accountGroupId);
        foreach ($accountGroupList as $accountGroup) {
            $accountItems = [];
            /** @var array{
             *   account_id: string,
             *   account_group_id: string,
             *   account_title: string,
             *   description: string,
             *   selectable: bool,
             *   bk_uid: int|null,
             *   account_bk_code: int|null,
             *   display_order: int|null,
             *   created_at: string|null,
             *   updated_at: string|null,
             *   deleted_at: string|null,
             * }[] $accountItemList
             */
            $accountItemList = $this->account->searchAccountGropupForExporting($accountGroupId, $accountId);
            foreach ($accountItemList as $accountItem) {
                /** @var array{
                 *   account_id: string,
                 *   account_group_id: string,
                 *   account_title: string,
                 *   description: string,
                 *   selectable: bool,
                 *   bk_uid: int|null,
                 *   account_bk_code: int|null,
                 *   display_order: int|null,
                 *   updated_at: string|null,
                 *   deleted: bool,
                 * } $convertedAccountItem
                 */
                $convertedAccountItem = $this->convertExportedTimestamps($accountItem);
                $accountItems[$accountItem['account_id']] = $convertedAccountItem;
            }
            $accountGroups[$accountGroup['account_group_id']] = ['items' => $accountItems];
        }

        return $accountGroups;
    }

    /**
     * Export a list of account items belonging to the account group.
     *
     * @param  string  $bookId
     * @param  string  $accountGroupId
     * @return array<string, array{
     *   items: array<string, array{
     *     account_id: string,
     *     updated_at: string|null,
     *   }>,
     * }>
     */
    public function exportAccountItems($bookId, $accountGroupId): array
    {
        $accountGroups = [];

        /** @var array{
         *   account_group_id: string,
         *   book_id: string,
         *   account_type: string,
         *   account_group_title: string,
         *   bk_uid: int|null,
         *   account_group_bk_code: int|null,
         *   is_current: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $accountGroupList
         */
        $accountGroupList = $this->accountGroup->searchBookForExporting($bookId, $accountGroupId);
        foreach ($accountGroupList as $accountGroup) {
            $accountItems = [];
            /** @var array{
             *   account_id: string,
             *   account_group_id: string,
             *   account_title: string,
             *   description: string,
             *   selectable: bool,
             *   bk_uid: int|null,
             *   account_bk_code: int|null,
             *   display_order: int|null,
             *   created_at: string|null,
             *   updated_at: string|null,
             *   deleted_at: string|null,
             * }[] $accountItemList
             */
            $accountItemList = $this->account->searchAccountGropupForExporting(strval($accountGroup['account_group_id']));
            foreach ($accountItemList as $accountItem) {
                $accountItems[$accountItem['account_id']] = [
                    'account_id' => $accountItem['account_id'],
                    'updated_at' => $accountItem['updated_at'],
                ];
            }
            $accountGroups[$accountGroup['account_group_id']] = ['items' => $accountItems];
        }

        return $accountGroups;
    }

    /**
     * Export accounts of the book.
     *
     * @param  string  $bookId
     * @return array<string, array{
     *   account_group_id: string,
     *   updated_at: string|null,
     * }>
     */
    public function exportAccounts($bookId): array
    {
        $accountGroups = [];

        /** @var array{
         *   account_group_id: string,
         *   book_id: string,
         *   account_type: string,
         *   account_group_title: string,
         *   bk_uid: int|null,
         *   account_group_bk_code: int|null,
         *   is_current: bool,
         *   display_order: int|null,
         *   created_at: string|null,
         *   updated_at: string|null,
         *   deleted_at: string|null,
         * }[] $accountGroupList
         */
        $accountGroupList = $this->accountGroup->searchBookForExporting($bookId);
        foreach ($accountGroupList as $accountGroup) {
            $accountGroupId = $accountGroup['account_group_id'];
            $accountGroups[$accountGroupId] = [
                'account_group_id' => $accountGroup['account_group_id'],
                'updated_at'       => $accountGroup['updated_at'],
            ];
        }

        return $accountGroups;
    }

    /**
     * Import the account group.
     *
     * @param  string  $sourceUrl
     * @param  string  $accessToken
     * @param  string  $bookId
     * @param  array{
     *   account_group_id: string,
     *   updated_at: string|null,
     * }  $accountGroup
     * @param  array<string, array{
     *   account_group_id: string,
     *   updated_at: string|null,
     * }>  $destinationAccountGroups
     * @return array<string, mixed>
     */
    public function importAccountGroup($sourceUrl, $accessToken, $bookId, array $accountGroup, array $destinationAccountGroups): array
    {
        $mode = null;
        $result = null;
        $accountGroupId = $accountGroup['account_group_id'];
        if (key_exists($accountGroupId, $destinationAccountGroups)) {
            $sourceUpdateAt = $accountGroup['updated_at'];
            $destinationUpdateAt = $destinationAccountGroups[$accountGroupId]['updated_at'];
            if ($this->isSourceLater($sourceUpdateAt, $destinationUpdateAt)) {
                $mode = 'update';
            }
        } else {
            $mode = 'create';
        }
        if (isset($mode)) {
            $response = Http::withToken($accessToken)->get(
                $sourceUrl.'/'.$bookId.'/accounts/'.$accountGroupId
            );
            if ($response->ok()) {
                /** @var array{
                 *   version: string,
                 *   books: array<string, array{
                 *     accounts: array<string, array{
                 *       account_group_id: string,
                 *       book_id: string,
                 *       account_type: string,
                 *       account_group_title: string,
                 *       bk_uid: int|null,
                 *       account_group_bk_code: int|null,
                 *       is_current: bool,
                 *       display_order: int|null,
                 *       updated_at: string|null,
                 *       deleted: bool,
                 *     }>,
                 *   }>,
                 * } $responseBody
                 */
                $responseBody = $response->json();
                $accountGroup = $responseBody['books'][$bookId]['accounts'][$accountGroupId];
                switch($mode) {
                    case 'update':
                        $this->accountGroup->updateForImporting($accountGroup);
                        $result = 'updated';
                        break;
                    case 'create':
                        $this->accountGroup->createForImporting($accountGroup);
                        $result = 'created';
                        break;
                    default:
                        break;
                }
            }
        } else {
            $result = 'already up-to-date';
        }

        return ['account_group_id' => $accountGroupId, 'result' => $result];
    }

    /**
     * Import a list of account items belonging to the account group.
     *
     * @param  string  $sourceUrl
     * @param  string  $accessToken
     * @param  string  $bookId
     * @param  string  $accountGroupId
     * @param  array{
     *   account_id: string,
     *   updated_at: string|null,
     * }  $accountItem
     * @param  array<string, array{
     *   account_id: string,
     *   updated_at: string|null,
     * }>  $destinationAccountItems
     * @return array<string, mixed>
     */
    public function importAccountItem($sourceUrl, $accessToken, $bookId, $accountGroupId, array $accountItem, array $destinationAccountItems): array
    {
        $mode = null;
        $result = null;
        $accountId = $accountItem['account_id'];
        if (key_exists($accountId, $destinationAccountItems)) {
            $sourceUpdateAt = $accountItem['updated_at'];
            $destinationUpdateAt = $destinationAccountItems[$accountId]['updated_at'];
            if ($this->isSourceLater($sourceUpdateAt, $destinationUpdateAt)) {
                $mode = 'update';
            }
        } else {
            $mode = 'create';
        }
        if (isset($mode)) {
            $response = Http::withToken($accessToken)->get(
                $sourceUrl.'/'.$bookId.'/accounts/'.$accountGroupId.'/items/'.$accountId
            );
            if ($response->ok()) {
                /** @var array{
                 *   version: string,
                 *   books: array<string, array{
                 *     accounts: array<string, array{
                 *       items: array<string, array{
                 *         account_id: string,
                 *         account_group_id: string,
                 *         account_title: string,
                 *         description: string,
                 *         selectable: bool,
                 *         bk_uid: int|null,
                 *         account_bk_code: int|null,
                 *         display_order: int|null,
                 *         updated_at: string|null,
                 *         deleted: bool,
                 *       }>,
                 *     }>,
                 *   }>,
                 * } $responseBody
                 */
                $responseBody = $response->json();
                $accountItem = $responseBody['books'][$bookId]['accounts'][$accountGroupId]['items'][$accountId];
                switch($mode) {
                    case 'update':
                        $this->account->updateForImporting($accountItem);
                        $result = 'updated';
                        break;
                    case 'create':
                        $this->account->createForImporting($accountItem);
                        $result = 'created';
                        break;
                    default:
                        break;
                }
            } else {
                $result = 'response error('.$response->status().')';
            }
        } else {
            $result = 'already up-to-date';
        }

        return ['account_id' => $accountId, 'result' => $result];
    }

    /**
     * Import a list of account items belonging to the account group.
     *
     * @param  string  $sourceUrl
     * @param  string  $accessToken
     * @param  string  $bookId
     * @param  string  $accountGroupId
     * @return array<string, mixed>
     */
    public function importAccountItems($sourceUrl, $accessToken, $bookId, $accountGroupId): array
    {
        $result = [];

        $destinationAccountItems = $this->exportAccountItems($bookId, $accountGroupId);
        $debug['destinationAccountItems'] = $destinationAccountItems;
        $response = Http::withToken($accessToken)->get(
            $sourceUrl.'/'.$bookId.'/accounts/'.$accountGroupId.'/items'
        );
        if ($response->ok()) {
            /** @var array{
             *   version: string,
             *   books: array<string, array{
             *     accounts: array<string, array{
             *       items: array<string, array{
             *         account_id: string,
             *         updated_at: string|null,
             *       }>,
             *     }>,
             *   }>,
             * } $responseBody
             */
            $responseBody = $response->json();
            $sourceAccountItems = $responseBody['books'][$bookId]['accounts'][$accountGroupId]['items'];
            foreach ($sourceAccountItems as $accountId => $accountItem) {
                $result[$accountId] = $this->importAccountItem(
                    $sourceUrl,
                    $accessToken,
                    $bookId,
                    $accountGroupId,
                    $accountItem,
                    $destinationAccountItems[$accountGroupId]['items']
                );
            }
        }
        //$result['debug'] = $debug;

        return $result;
    }

    /**
     * Import accounts of the book.
     *
     * @param  string  $sourceUrl
     * @param  string  $accessToken
     * @param  string  $bookId
     * @return array<string, mixed>
     */
    public function importAccounts($sourceUrl, $accessToken, $bookId): array
    {
        $result = [];

        $debug = ['sourceUrl' => $sourceUrl, 'accessToken' => $accessToken, 'bookId'=> $bookId];
        $destinationAccountGroups = $this->exportAccounts($bookId);
        $debug['destinationAccountGropus'] = $destinationAccountGroups;
        $response = Http::withToken($accessToken)->get($sourceUrl.'/'.$bookId.'/accounts');
        if ($response->ok()) {
            /** @var array{
             *   version: string,
             *   books: array<string, array{
             *     accounts: array<string, array{
             *       account_group_id: string,
             *       updated_at: string|null,
             *     }>,
             *   }>,
             * } $responseBody
             */
            $responseBody = $response->json();
            $sourceAccountGropus = $responseBody['books'][$bookId]['accounts'];
            $debug['sourceAccountGropus'] = $sourceAccountGropus;
            foreach ($sourceAccountGropus as $accountGroupId => $accountGroup) {
                $result[$accountGroupId] = $this->importAccountGroup(
                    $sourceUrl, $accessToken, $bookId, $accountGroup, $destinationAccountGroups
                );
                $result[$accountGroupId]['items'] = $this->importAccountItems(
                    $sourceUrl, $accessToken, $bookId, $accountGroupId
                );
            }
        }
        //$result['debug'] = $debug;

        return $result;
    }

    /**
     * Retrieve a list of accounts of the book.
     *
     * @param  string  $bookId
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
    public function retrieveAccounts($bookId): array
    {
        $accounts = [];

        $accountList = $this->account->searchBook($bookId);
        foreach ($accountList as $accountItem) {
            $accounts[strval($accountItem['account_id'])] = [
                'account_type'             => strval($accountItem['account_type']),
                'account_group_id'         => strval($accountItem['account_group_id']),
                'account_group_title'      => strval($accountItem['account_group_title']),
                'is_current'               => boolval($accountItem['is_current']),
                'account_id'               => strval($accountItem['account_id']),
                'account_title'            => strval($accountItem['account_title']),
                'description'              => strval($accountItem['description']),
                'selectable'               => boolval($accountItem['selectable']),
                'account_bk_code'          => intval($accountItem['account_bk_code']),
                'created_at'               => strval($accountItem['created_at']),
                'account_group_bk_code'    => intval($accountItem['account_group_bk_code']),
                'account_group_created_at' => strval($accountItem['account_group_created_at']),
            ];
        }

        return $accounts;
    }

    /**
     * Retrieve a list of account groups of the book.
     *
     * @param  string  $bookId
     * @return array<string, array{
     *   account_group_id: string,
     *   account_type: string,
     *   account_group_title: string,
     *   is_current: bool,
     *   account_group_bk_code: int,
     *   created_at: string,
     * }>
     */
    public function retrieveAccountGroups($bookId): array
    {
        $accountGroups = [];

        $accountGroupList = $this->accountGroup->searchBook($bookId);
        foreach ($accountGroupList as $accountGroup) {
            $accountGroups[strval($accountGroup['account_group_id'])] = [
                'account_group_id'      => strval($accountGroup['account_group_id']),
                'account_type'          => strval($accountGroup['account_type']),
                'account_group_title'   => strval($accountGroup['account_group_title']),
                'is_current'            => boolval($accountGroup['is_current']),
                'account_group_bk_code' => intval($accountGroup['account_group_bk_code']),
                'created_at'            => strval($accountGroup['created_at']),
            ];
        }

        return $accountGroups;
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
     * @return void
     */
    public function updateAccount($accountId, array $newData)
    {
        $this->account->update($accountId, $newData);
    }

    /**
     * Update the account group.
     *
     * @param  string  $accountGroupId
     * @param  array{
     *   title?: string,
     *   is_current?: bool,
     * }  $newData
     * @return void
     */
    public function updateAccountGroup($accountGroupId, array $newData)
    {
        $this->accountGroup->update($accountGroupId, $newData);
    }

    /**
     * Check if the source is later than the destination.
     *
     * @param  string|null  $sourceUpdateAt
     * @param  string|null  $destinationUpdateAt
     * @return bool
     */
    private function isSourceLater($sourceUpdateAt, $destinationUpdateAt)
    {
        if (isset($sourceUpdateAt)) {
            $source = Carbon::createFromFormat(Carbon::ATOM, $sourceUpdateAt);
            if (! is_bool($source)) {
                if (isset($destinationUpdateAt)) {
                    $destination = Carbon::createFromFormat(Carbon::ATOM, $destinationUpdateAt);
                    if (! is_bool($destination)) {
                        return $source->gt($destination);
                    } else {
                        return true;
                    }
                } else {
                    return true;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Convert exported timestamps.
     *
     * @param  array<string, mixed>  $exported
     * @return array<string, mixed>
     */
    private function convertExportedTimestamps(array $exported)
    {
        $converted = [];
        foreach ($exported as $key => $value) {
            switch ($key) {
                case 'created_at':
                    break;
                case 'deleted_at':
                    $converted['deleted'] = ! is_null($value);
                    break;
                default:
                    $converted[$key] = $value;
                    break;
            }
        }

        return $converted;
    }
}
