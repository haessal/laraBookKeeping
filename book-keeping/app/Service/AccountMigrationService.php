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
    protected $tools;

    /**
     * Create a new AccountMigrationService instance.
     *
     * @param  \App\DataProvider\AccountRepositoryInterface  $account
     * @param  \App\DataProvider\AccountGroupRepositoryInterface  $accountGroup
     * @param  \App\Service\BookKeepingMigrationTools  $tools
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
     * @return array{
     *   account_group_id: string,
     *   account_group: array{
     *     account_group_id: string,
     *     book_id: string,
     *     account_type: string,
     *     account_group_title: string,
     *     bk_uid: int|null,
     *     account_group_bk_code: int|null,
     *     is_current: bool,
     *     display_order: int|null,
     *     updated_at: string|null,
     *     deleted: bool,
     *   },
     *   items: array{
     *     account_id: string,
     *     account: array{
     *       account_id: string,
     *       account_group_id: string,
     *       account_title: string,
     *       description: string,
     *       selectable: bool,
     *       bk_uid: int|null,
     *       account_bk_code: int|null,
     *       display_order: int|null,
     *       updated_at: string|null,
     *       deleted: bool,
     *     },
     *   }[],
     * }[]
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
                $accountItems[] = [
                    'account_id' => $accountItem['account_id'],
                    'account' => $convertedAccountItem,
                ];
            }
            $accountGroups[] = [
                'account_group_id' => $accountGroupId,
                'account_group' => $convertedAccountGroup,
                'items' => $accountItems,
            ];
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
                'updated_at' => $accountGroup['updated_at'],
            ];
        }

        return $accountGroups;
    }
}
