<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\AccountGroupRepositoryInterface;

class AccountGroupRepository implements AccountGroupRepositoryInterface
{
    /**
     * Create new account group.
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
    public function create(string $bookId, string $accountType, string $title, bool $isCurrent, $bk_uid, $bk_code): string
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
     * Search account group.
     *
     * @param string $bookId
     *
     * @return array
     */
    public function search(string $bookId): array
    {
        $list = AccountGroup::select(
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
     * Search account group.
     *
     * @param string $bookId
     *
     * @return array
     */
    public function searchForExport(string $bookId): array
    {
        $list = AccountGroup::where('book_id', $bookId)
            ->get()->toArray();

        return $list;
    }

    /**
     * Update account group.
     *
     * @param string $accountGroupId
     * @param array  $newData
     */
    public function update(string $accountGroupId, array $newData)
    {
        $accountGroup = AccountGroup::find($accountGroupId);
        if (!is_null($accountGroup)) {
            if (array_key_exists('title', $newData)) {
                $accountGroup->account_group_title = $newData['title'];
            }
            if (array_key_exists('is_current', $newData)) {
                $accountGroup->is_current = $newData['is_current'];
            }
            $accountGroup->save();
        }
    }
}
