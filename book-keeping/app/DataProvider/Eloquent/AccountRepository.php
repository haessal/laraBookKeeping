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
}
