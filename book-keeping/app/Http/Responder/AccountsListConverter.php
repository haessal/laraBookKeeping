<?php

namespace App\Http\Responder;

trait AccountsListConverter
{
    /**
     * Sort accounts in ascending order of account code for version 1.
     *
     * @param  array  $groupedList
     * @return array
     */
    public function sortAccountInAscendingCodeOrder(array $groupedList): array
    {
        return $this->sortAccountGroupListInAscendingCodeOrder($groupedList);
    }

    /**
     * Translate account list to title list for view.
     *
     * @param  array  $accounts
     * @param  bool  $withGroupList
     * @return array
     */
    public function translateAccountListToTitleList(array $accounts, bool $withGroupList = false): array
    {
        $accountItem_title_list = [];
        $accountGroup_title_list = [];
        $accountTypeGroup_title_list = [];

        $accountTypeCaption = [
            'asset'     => __('Assets'),
            'liability' => __('Liabilities'),
            'expense'   => __('Expense'),
            'revenue'   => __('Revenue'),
        ];
        foreach ($accounts as $accountTypeKey => $accountType) {
            foreach ($accountType['groups'] as $accountGroupId => $accountGroupItem) {
                $accountGroup_title_list[$accountGroupId] = $accountGroupItem['title'];
                $accountTypeGroup_title_list[$accountGroupId] = $accountTypeCaption[$accountTypeKey].' - '.$accountGroupItem['title'];
                foreach ($accountGroupItem['items'] as $accountId => $accountItem) {
                    $accountItem_title_list[$accountId] = $accountItem['title'];
                }
            }
        }
        if ($withGroupList) {
            $account_title_list = [
                'groups'         => $accountGroup_title_list,
                'groupsWithType' => $accountTypeGroup_title_list,
                'items'          => $accountItem_title_list,
            ];
        } else {
            $account_title_list = $accountItem_title_list;
        }

        return $account_title_list;
    }

    /**
     * Get associative array which has account ID as key sorted in ascending order of value that is specified keyword.
     *
     * @param  array  $listWithKeyword
     * @return array
     */
    private function getIdsSortedInAscendingOrder(array $listWithKeyword): array
    {
        $sortedIds_isCurrent_withCode = [];
        $sortedIds_isCurrent_withoutCode = [];
        $sortedIds_isNotCurrent_withCode = [];
        $sortedIds_isNotCurrent_withoutCode = [];
        foreach ($listWithKeyword as $Ids => $item) {
            if (array_key_exists('isCurrent', $item) && ($item['isCurrent'] == true)) {
                if (isset($item['bk_code']) && ($item['bk_code'] != 0)) {
                    $sortedIds_isCurrent_withCode[$Ids] = $item['bk_code'];
                } else {
                    $sortedIds_isCurrent_withoutCode[$Ids] = $item['createdAt'];
                }
            } else {
                if (isset($item['bk_code']) && ($item['bk_code'] != 0)) {
                    $sortedIds_isNotCurrent_withCode[$Ids] = $item['bk_code'];
                } else {
                    $sortedIds_isNotCurrent_withoutCode[$Ids] = $item['createdAt'];
                }
            }
        }
        asort($sortedIds_isCurrent_withCode);
        asort($sortedIds_isCurrent_withoutCode);
        asort($sortedIds_isNotCurrent_withCode);
        asort($sortedIds_isNotCurrent_withoutCode);

        return $sortedIds_isCurrent_withCode + $sortedIds_isCurrent_withoutCode + $sortedIds_isNotCurrent_withCode + $sortedIds_isNotCurrent_withoutCode;
    }

    /**
     * Sort account group list in ascending order of account code for version 1.
     *
     * @param  array  $groupedList
     * @return array
     */
    private function sortAccountGroupListInAscendingCodeOrder(array $groupedList): array
    {
        $reordered = [];
        $sortedKeys = $this->getIdsSortedInAscendingOrder($groupedList);
        foreach ($sortedKeys as $groupId => $keyword) {
            $reordered[$groupId] = $groupedList[$groupId];
            $reordered[$groupId]['items'] = $this->sortAccountListInAscendingCodeOrder($groupedList[$groupId]['items']);
        }

        return $reordered;
    }

    /**
     * Sort account list in ascending order of account code for version 1.
     *
     * @param  array  $groupedList
     * @return array
     */
    private function sortAccountListInAscendingCodeOrder(array $list): array
    {
        $reordered = [];
        $sortedKeys = $this->getIdsSortedInAscendingOrder($list);
        foreach ($sortedKeys as $id => $keyword) {
            $reordered[$id] = $list[$id];
        }

        return $reordered;
    }
}
