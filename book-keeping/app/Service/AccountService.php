<?php

namespace App\Service;

use App\DataProvider\AccountGroupRepositoryInterface;
use App\DataProvider\AccountRepositoryInterface;
use Illuminate\Support\Carbon;

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
     * @param \App\DataProvider\AccountRepositoryInterface      $account
     * @param \App\DataProvider\AccountGroupRepositoryInterface $accountGroup
     */
    public function __construct(AccountRepositoryInterface $account, AccountGroupRepositoryInterface $accountGroup)
    {
        $this->account = $account;
        $this->accountGroup = $accountGroup;
    }

    /**
     * Create new Account.
     *
     * @param string $accountGroupId
     * @param string $title
     * @param string $description
     * @param int    $bk_uid
     * @param int    $bk_code
     *
     * @return string $accountId
     */
    public function createAccount(string $accountGroupId, string $title, string $description, int $bk_uid = null, int $bk_code = null): string
    {
        $accountId = $this->account->create($accountGroupId, $title, $description, $bk_uid, $bk_code);

        return $accountId;
    }

    /**
     * Create new Account Group.
     *
     * @param string $bookId
     * @param string $accountType
     * @param string $title
     * @param bool   $isCurrent
     * @param int    $bk_uid
     * @param int    $bk_code
     *
     * @return string $accountGroupId
     */
    public function createAccountGroup(string $bookId, string $accountType, string $title, bool $isCurrent = false, int $bk_uid = null, int $bk_code = null): string
    {
        $accountGroupId = $this->accountGroup->create($bookId, $accountType, $title, $isCurrent, $bk_uid, $bk_code);

        return $accountGroupId;
    }

    /**
     * Export grouped account list.FIXME
     *
     * @param string $bookId
     * @param string $accountGroupId
     *
     * @return array
     */
    public function exportAccountGroup(string $bookId, string $accountGroupId): array
    {
        $accountGroups = [];

        $accountGroupList = $this->accountGroup->searchForExport($bookId, $accountGroupId);
        foreach ($accountGroupList as $accountGroup) {
            $accountGroups[$accountGroup['account_group_id']] = $this->convertExportedData($accountGroup);
        }

        return $accountGroups;
    }

    /**
     * Export grouped account list.FIXME
     *
     * @param string $bookId
     * @param string $accountGroupId
     * @param string $accountId
     *
     * @return array
     */
    public function exportAccountItem(string $bookId, string $accountGroupId, string $accountId): array
    {
        $accountGroups = [];

        $accountGroupList = $this->accountGroup->searchForExport($bookId, $accountGroupId);
        foreach ($accountGroupList as $accountGroup) {
            $accountItems = [];
            $accountItemList = $this->account->searchAccountForExport($accountGroup['account_group_id'], $accountId);
            foreach ($accountItemList as $accountItem) {
                $accountItems[$accountItem['account_id']] = $this->convertExportedData($accountItem);
            }
            $accountGroups[$accountGroup['account_group_id']] = ['items' => $accountItems];
        }

        return $accountGroups;
    }

    /**
     * Export grouped account list.FIXME
     *
     * @param string $bookId
     * @param string $accountGroupId
     *
     * @return array
     */
    public function exportAccountItems(string $bookId, string $accountGroupId): array
    {
        $accountGroups = [];

        $accountGroupList = $this->accountGroup->searchForExport($bookId, $accountGroupId);
        foreach ($accountGroupList as $accountGroup) {
            $accountItems = [];
            $accountItemList = $this->account->searchAccountForExport($accountGroup['account_group_id']);
            foreach ($accountItemList as $accountItem) {
                $accountItems[$accountItem['account_id']] = $this->convertExportedData([
                    'account_id' => $accountItem['account_id'],
                    'updated_at' => $accountItem['updated_at'],
                ]);
            }
            $accountGroups[$accountGroup['account_group_id']] = ['items' => $accountItems];
        }

        return $accountGroups;
    }

    /**
     * Export grouped account list.FIXME
     *
     * @param string $bookId
     * @param bool  $dumpRequired
     *
     * @return array
     */
    public function exportAccounts(string $bookId, bool $dumpRequired): array
    {
        $accountGroups = [];

        $accountGroupList = $this->accountGroup->searchForExport($bookId);
        foreach ($accountGroupList as $accountGroup) {
            $accountGroupId = $accountGroup['account_group_id'];
            if ($dumpRequired) {
                $convertedAccountGroup = $this->convertExportedData($accountGroup);

                $accountItems = [];
                $accountItemList = $this->account->searchAccountForExport($accountGroupId);
                foreach ($accountItemList as $accountItem) {
                    $convertedAccountItem = $this->convertExportedData($accountItem);
                    $accountItems[] = [
                        'account_id' => $accountItem['account_id'],
                        'account'    => $convertedAccountItem,
                    ];
                }

                $accountGroups[] = [
                    'account_group_id' => $accountGroupId,
                    'account_group'    => $convertedAccountGroup,
                    'items'            => $accountItems,
                ];
            } else {
                $accountGroups[$accountGroupId] = $this->convertExportedData([
                    'account_group_id' => $accountGroup['account_group_id'],
                    'updated_at'       => $accountGroup['updated_at'],
                ]);
            }
        }

        return $accountGroups;
    }

    /**
     * Retrieve list of account.
     *
     * @param string $bookId
     *
     * @return array
     */
    public function retrieveAccounts(string $bookId): array
    {
        $accounts = [];
        $accountList = $this->account->searchAccount($bookId);

        foreach ($accountList as $accountItem) {
            $accounts[$accountItem['account_id']] = $accountItem;
        }

        return $accounts;
    }

    /**
     * Retrieve list of account group.
     *
     * @param string $bookId
     *
     * @return array
     */
    public function retrieveAccountGroups(string $bookId): array
    {
        $accountGroups = [];
        $accountGroupList = $this->accountGroup->search($bookId);

        foreach ($accountGroupList as $accountGroup) {
            $accountGroups[$accountGroup['account_group_id']] = $accountGroup;
        }

        return $accountGroups;
    }

    /**
     * Update Account.
     *
     * @param string $accountId
     * @param array  $newData
     */
    public function updateAccount(string $accountId, array $newData)
    {
        $this->account->update($accountId, $newData);
    }

    /**
     * Update Account Group.
     *
     * @param string $accountGroupId
     * @param array  $newData
     */
    public function updateAccountGroup(string $accountGroupId, array $newData)
    {
        $this->accountGroup->update($accountGroupId, $newData);
    }

    private function convertExportedData(array $exported)
    {
        $converted = [];
        foreach ($exported as $key => $value) {
            switch ($key) {
                case 'created_at':
                    break;
                case 'updated_at':
                    $d = Carbon::createFromFormat('Y-m-d H:i:s', $value);
                    $converted['updated_at'] = $d->toAtomString();
                    break;
                case 'deleted_at':
                    $converted['deleted'] = !is_null($value);
                    break;
                default:
                    $converted[$key] = $value;
                    break;
            }
        }

        return $converted;
    }
}
