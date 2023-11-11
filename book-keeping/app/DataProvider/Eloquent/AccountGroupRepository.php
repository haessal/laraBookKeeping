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
     * Create a new account group to import.
     *
     * @param  array{
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
     * }  $newAccountGroup
     * @return void
     */
    public function createForImporting(array $newAccountGroup)
    {
        $accountGroup = new AccountGroup();
        $accountGroup->account_group_id = $newAccountGroup['account_group_id'];
        $accountGroup->book_id = $newAccountGroup['book_id'];
        $accountGroup->account_type = $newAccountGroup['account_type'];
        $accountGroup->account_group_title = $newAccountGroup['account_group_title'];
        $accountGroup->bk_uid = $newAccountGroup['bk_uid'];
        $accountGroup->account_group_bk_code = $newAccountGroup['account_group_bk_code'];
        $accountGroup->is_current = $newAccountGroup['is_current'];
        $accountGroup->display_order = $newAccountGroup['display_order'];
        $accountGroup->save();
        $accountGroup->refresh();
        if ($newAccountGroup['deleted']) {
            $accountGroup->delete();
        }
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

    /**
     * Update the account group to import.
     *
     * @param  array{
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
     * }  $newAccountGroup
     * @return void
     */
    public function updateForImporting(array $newAccountGroup)
    {
        /** @var \App\Models\AccountGroup|null $accountGroup */
        $accountGroup = AccountGroup::withTrashed()->find($newAccountGroup['account_group_id']);
        if (! is_null($accountGroup)) {
            $accountGroup->book_id = $newAccountGroup['book_id'];
            $accountGroup->account_type = $newAccountGroup['account_type'];
            $accountGroup->account_group_title = $newAccountGroup['account_group_title'];
            $accountGroup->bk_uid = $newAccountGroup['bk_uid'];
            $accountGroup->account_group_bk_code = $newAccountGroup['account_group_bk_code'];
            $accountGroup->is_current = $newAccountGroup['is_current'];
            $accountGroup->display_order = $newAccountGroup['display_order'];
            $accountGroup->touch();
            $accountGroup->save();
            $accountGroup->refresh();
            if ($accountGroup->trashed()) {
                if (! $newAccountGroup['deleted']) {
                    $accountGroup->restore();
                }
            } else {
                if ($newAccountGroup['deleted']) {
                    $accountGroup->delete();
                }
            }
        }
    }
}
