<?php

namespace App\Service;

use App\DataProvider\AccountGroupRepositoryInterface;
use App\DataProvider\AccountRepositoryInterface;

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
     * Export grouped account list.
     *
     * @param string $bookId
     *
     * @return array
     */
    public function exportAccounts(string $bookId): array
    {
        $accountGroups = [];
        $accountGroupList = $this->accountGroup->searchForExport($bookId);

        foreach ($accountGroupList as $accountGroup) {
            $accountGroupId = $accountGroup['account_group_id'];
            $accountGroups[$accountGroupId] = $accountGroup;

            $accountItems = [];
            $accountItemList = $this->account->searchAccountForExport($accountGroupId);
            foreach ($accountItemList as $accountItem) {
                $accountItems[$accountItem['account_id']] = $accountItem;
            }

            $accountGroups[$accountGroupId]['items'] = $accountItems;
        }

        return $accountGroups;
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
}
