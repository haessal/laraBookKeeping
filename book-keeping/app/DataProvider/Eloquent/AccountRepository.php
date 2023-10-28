<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\AccountRepositoryInterface;
use App\Models\Account;

class AccountRepository implements AccountRepositoryInterface
{
    /**
     * Create a new account to be bound in the account group.
     *
     * @param  string  $accountGroupId
     * @param  string  $title
     * @param  string  $description
     * @param  int|null  $bk_uid
     * @param  int|null  $bk_code
     * @return string
     */
    public function create($accountGroupId, $title, $description, $bk_uid, $bk_code)
    {
        $account = new Account();
        $account->account_group_id = $accountGroupId;
        $account->account_title = $title;
        $account->description = $description;
        $account->selectable = true;
        $account->bk_uid = $bk_uid;
        $account->account_bk_code = $bk_code;
        $account->save();

        return $account->account_id;
    }

    /**
     * Create a new account to import.
     *
     * @param  array{
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
     * }  $newAccount
     * @return void
     */
    public function createForImporting(array $newAccount)
    {
        // FIXME
        return;
    }

    /**
     * Search the account group for account items to export.
     *
     * @param  string  $accountGroupId
     * @param  string|null  $accountId
     * @return array<int, array<string, mixed>>
     */
    public function searchAccountGropupForExporting($accountGroupId, $accountId = null): array
    {
        /** @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query */
        $query = Account::withTrashed()
            ->select('*')
            ->where('account_group_id', $accountGroupId);
        if (isset($accountId)) {
            $query = $query->where('account_id', $accountId);
        }
        /** @var array<int, array<string, mixed>> $list */
        $list = $query->get()->toArray();

        return $list;
    }

    /**
     * Search the book for accounts.
     *
     * @param  string  $bookId
     * @return array<int, array<string, mixed>>
     */
    public function searchBook($bookId): array
    {
        /** @var array<int, array<string, mixed>> $list */
        $list = Account::query()->select(
            'account_type',
            'bk2_0_account_groups.account_group_id',
            'account_group_title',
            'is_current',
            'account_id',
            'account_title',
            'description',
            'selectable',
            'account_bk_code',
            'bk2_0_accounts.created_at',
            'account_group_bk_code',
            'bk2_0_account_groups.created_at as account_group_created_at',
        )
            ->join('bk2_0_account_groups', 'bk2_0_account_groups.account_group_id', '=', 'bk2_0_accounts.account_group_id')
            ->where('book_id', $bookId)
            ->whereNull('bk2_0_account_groups.deleted_at')
            ->whereNull('bk2_0_accounts.deleted_at')
            ->orderBy('account_type')
            ->orderBy('account_group_id')
            ->get()->toArray();

        return $list;
    }

    /**
     * Update the account.
     *
     * @param  string  $accountId
     * @param  array<string, mixed>  $newData
     * @return void
     */
    public function update($accountId, array $newData)
    {
        /** @var \App\Models\Account|null $account */
        $account = Account::query()->find($accountId);
        if (! is_null($account)) {
            if (array_key_exists('group', $newData)) {
                $account->account_group_id = strval($newData['group']);
            }
            if (array_key_exists('title', $newData)) {
                $account->account_title = strval($newData['title']);
            }
            if (array_key_exists('description', $newData)) {
                $account->description = strval($newData['description']);
            }
            if (array_key_exists('selectable', $newData)) {
                $account->selectable = boolval($newData['selectable']);
            }
            $account->save();
        }
    }

    /**
     * Update the account to import.
     *
     * @param  array{
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
     * }  $newAccount
     * @return void
     */
    public function updateForImporting(array $newAccount)
    {
        // FIXME
        return;
    }
}
