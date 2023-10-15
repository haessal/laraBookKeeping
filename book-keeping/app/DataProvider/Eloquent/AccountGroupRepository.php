<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\AccountGroupRepositoryInterface;
use App\Models\AccountGroup;

class AccountGroupRepository implements AccountGroupRepositoryInterface
{
    /**
     * Create a new account group to be bound in the book.
     *
     * @param  string  $bookId
     * @param  string  $accountType
     * @param  string  $title
     * @param  bool  $isCurrent
     * @param  int|null  $bk_uid
     * @param  int|null  $bk_code
     * @return string
     */
    public function create($bookId, $accountType, $title, $isCurrent, $bk_uid, $bk_code)
    {
        $accountGroup = new AccountGroup();
        $accountGroup->book_id = $bookId;
        $accountGroup->account_type = $accountType;
        $accountGroup->account_group_title = $title;
        $accountGroup->is_current = $isCurrent;
        $accountGroup->bk_uid = $bk_uid;
        $accountGroup->account_group_bk_code = $bk_code;
        $accountGroup->save();

        return $accountGroup->account_group_id;
    }

    /**
     * Search the book for account groups.
     *
     * @param  string  $bookId
     * @return array<int, array<string, mixed>>
     */
    public function searchBook($bookId): array
    {
        /** @var array<int, array<string, mixed>> $list */
        $list = AccountGroup::query()
            ->select(
                'account_group_id',
                'account_type',
                'account_group_title',
                'is_current',
                'account_group_bk_code',
                'created_at',
            )
            ->where('book_id', $bookId)
            ->whereNull('deleted_at')
            ->orderBy('account_type')
            ->orderBy('account_group_id')
            ->get()->toArray();

        return $list;
    }

    /**
     * Search the book for account groups to export.
     *
     * @param  string  $bookId
     * @param  string|null  $accountGroupId
     * @return array<int, array<string, mixed>>
     */
    public function searchBookForExporting($bookId, $accountGroupId = null): array
    {
        /** @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query */
        $query = AccountGroup::withTrashed()
            ->select('*')
            ->where('book_id', $bookId);
        if (isset($accountGroupId)) {
            $query = $query->where('account_group_id', $accountGroupId);
        }
        /** @var array<int, array<string, mixed>> $list */
        $list = $query->get()->toArray();

        return $list;
    }

    /**
     * Update the account group.
     *
     * @param  string  $accountGroupId
     * @param  array<string, mixed>  $newData
     * @return void
     */
    public function update($accountGroupId, array $newData)
    {
        /** @var \App\Models\AccountGroup|null $accountGroup */
        $accountGroup = AccountGroup::query()->find($accountGroupId);
        if (! is_null($accountGroup)) {
            if (array_key_exists('title', $newData)) {
                $accountGroup->account_group_title = strval($newData['title']);
            }
            if (array_key_exists('is_current', $newData)) {
                $accountGroup->is_current = boolval($newData['is_current']);
            }
            $accountGroup->save();
        }
    }
}
