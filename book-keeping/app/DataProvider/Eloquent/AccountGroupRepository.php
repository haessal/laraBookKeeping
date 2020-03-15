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
     * Find the account groups bound in the book.
     *
     * @param string $bookId
     *
     * @return array
     */
    public function findAllByBoundIn(string $bookId): array
    {
        $list = AccountGroup::select(
            'account_type',
            'account_group_id',
            'account_group_title',
            'is_current',
            'bk_code',
            'created_at'
        )
            ->where('book_bound_on', $bookId)
            ->get()->toArray();

        return $list;
    }
}
