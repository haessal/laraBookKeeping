<?php

namespace App\Service;

use App\DataProvider\AccountGroupRepositoryInterface;
use App\DataProvider\AccountRepositoryInterface;

class AccountMigrationService extends AccountService
{
    /**
     * BookKeeping migration tools instance.
     *
     * @var \App\Service\BookKeepingMigrationTools
     */
    private $tools;

    /**
     * Create a new AccountMigrationService instance.
     *
     * @param  \App\DataProvider\AccountRepositoryInterface  $account
     * @param  \App\DataProvider\AccountGroupRepositoryInterface  $accountGroup
     * @param  \App\Service\BookKeepingMigrationTools $tools
     */
    public function __construct(AccountRepositoryInterface $account, AccountGroupRepositoryInterface $accountGroup, BookKeepingMigrationTools $tools)
    {
        parent::__construct($account, $accountGroup);
        $this->tools = $tools;
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
            $convertedAccountGroup = $this->tools->convertExportedTimestamps($accountGroup);
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
                $convertedAccountItem = $this->tools->convertExportedTimestamps($accountItem);
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
            $convertedAccountGroup = $this->tools->convertExportedTimestamps($accountGroup);
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
                $convertedAccountItem = $this->tools->convertExportedTimestamps($accountItem);
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
            if ($this->tools->isSourceLater($sourceUpdateAt, $destinationUpdateAt)) {
                $mode = 'update';
            }
        } else {
            $mode = 'create';
        }
        if (isset($mode)) {
            $response = $this->tools->getFromExporter(
                $sourceUrl.'/'.$bookId.'/accounts/'.$accountGroupId,
                $accessToken
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
            if ($this->tools->isSourceLater($sourceUpdateAt, $destinationUpdateAt)) {
                $mode = 'update';
            }
        } else {
            $mode = 'create';
        }
        if (isset($mode)) {
            $response = $this->tools->getFromExporter(
                $sourceUrl.'/'.$bookId.'/accounts/'.$accountGroupId.'/items/'.$accountId,
                $accessToken
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
        $response = $this->tools->getFromExporter(
            $sourceUrl.'/'.$bookId.'/accounts/'.$accountGroupId.'/items',
            $accessToken
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

        $destinationAccountGroups = $this->exportAccounts($bookId);
        $response = $this->tools->getFromExporter($sourceUrl.'/'.$bookId.'/accounts', $accessToken);
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
            foreach ($sourceAccountGropus as $accountGroupId => $accountGroup) {
                $result[$accountGroupId] = $this->importAccountGroup(
                    $sourceUrl, $accessToken, $bookId, $accountGroup, $destinationAccountGroups
                );
                $result[$accountGroupId]['items'] = $this->importAccountItems(
                    $sourceUrl, $accessToken, $bookId, $accountGroupId
                );
            }
        }

        return $result;
    }
}