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
