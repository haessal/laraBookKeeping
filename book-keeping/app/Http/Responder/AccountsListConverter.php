<?php

namespace App\Http\Responder;

trait AccountsListConverter
{
    /**
     * Sort accounts in ascending order of account code for version 1.
     *
     * @param  array<string, array{
     *   title: string,
     *   isCurrent: bool,
     *   amount?: int,
     *   bk_code: int,
     *   createdAt: string,
     *   items: array<string, array{
     *     title: string,
     *     amount?: int,
     *     description?: string,
     *     selectable?: bool,
     *     bk_code: int,
     *     createdAt: string,
     *   }>
     * }>  $groupedList
     * @return array<string, array{
     *   title: string,
     *   isCurrent: bool,
     *   amount: int,
     *   bk_code: int,
     *   createdAt: string,
     *   items: array<string, array{
     *     title: string,
     *     amount: int,
     *     description: string,
     *     selectable: bool,
     *     bk_code: int,
     *     createdAt: string,
     *   }>
     * }>
     */
    public function sortAccountInAscendingCodeOrder(array $groupedList): array
    {
        return $this->sortAccountGroupListInAscendingCodeOrder($groupedList);
    }

    /**
     * Translate account list to title list for view.
     *
     * @param  array<string, array{
     *   groups: array<string, array{
     *     title: string,
     *     items: array<string, array{
     *       title: string,
     *     }>
     *   }>
     * }>  $accounts
     * @param  bool  $withGroupList
     * @return array{
     *   groups: array<string, string>,
     *   groupsWithType: array<string, string>,
     *   items: array<string, string>
     * }|array<string, string>
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
                $accountTypeGroup_title_list[$accountGroupId]
                    = strval($accountTypeCaption[$accountTypeKey]).' - '.strval($accountGroupItem['title']);
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
     * Get associative array which has account ID as key sorted in ascending
     * order of value that is specified keyword.
     *
     * @param  array<string, array{
     *   isCurrent?: bool,
     *   bk_code: int|null,
     *   createdAt: string,
     * }>  $listWithKeyword
     * @return array<string, int|string|null>
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
     * @param  array<string, array{
     *   title: string,
     *   isCurrent: bool,
     *   amount?: int,
     *   bk_code: int,
     *   createdAt: string,
     *   items: array<string, array{
     *     title: string,
     *     amount?: int,
     *     description?: string,
     *     selectable?: bool,
     *     bk_code: int,
     *     createdAt: string,
     *   }>
     * }>  $groupedList
     * @return array<string, array{
     *   title: string,
     *   isCurrent: bool,
     *   amount: int,
     *   bk_code: int,
     *   createdAt: string,
     *   items: array<string, array{
     *     title: string,
     *     amount: int,
     *     description: string,
     *     selectable: bool,
     *     bk_code: int,
     *     createdAt: string,
     *   }>
     * }>
     */
    private function sortAccountGroupListInAscendingCodeOrder(array $groupedList): array
    {
        $reordered = [];
        $sortedKeys = $this->getIdsSortedInAscendingOrder($groupedList);
        foreach ($sortedKeys as $groupId => $keyword) {
            if (array_key_exists('amount', $groupedList[$groupId])) {
                $amount = $groupedList[$groupId]['amount'];
            } else {
                $amount = 0;
            }
            $reordered[$groupId] = [
                'title'     => $groupedList[$groupId]['title'],
                'isCurrent' => $groupedList[$groupId]['isCurrent'],
                'amount'    => $amount,
                'bk_code'   => $groupedList[$groupId]['bk_code'],
                'createdAt' => $groupedList[$groupId]['createdAt'],
                'items' => $this->sortAccountListInAscendingCodeOrder($groupedList[$groupId]['items']),
            ];
        }

        return $reordered;
    }

    /**
     * Sort account list in ascending order of account code for version 1.
     *
     * @param  array<string, array{
     *   title: string,
     *   amount?: int,
     *   description?: string,
     *   selectable?: bool,
     *   bk_code: int,
     *   createdAt: string,
     * }>  $list
     * @return array<string, array{
     *   title: string,
     *   amount: int,
     *   description: string,
     *   selectable: bool,
     *   bk_code: int,
     *   createdAt: string,
     * }>
     */
    private function sortAccountListInAscendingCodeOrder(array $list): array
    {
        $reordered = [];
        $sortedKeys = $this->getIdsSortedInAscendingOrder($list);
        foreach ($sortedKeys as $id => $keyword) {
            if (array_key_exists('amount', $list[$id])) {
                $amount = $list[$id]['amount'];
            } else {
                $amount = 0;
            }
            if (array_key_exists('description', $list[$id])) {
                $description = $list[$id]['description'];
            } else {
                $description = '';
            }
            if (array_key_exists('selectable', $list[$id])) {
                $selectable = $list[$id]['selectable'];
            } else {
                $selectable = true;
            }
            $reordered[$id] = [
                'title'       => $list[$id]['title'],
                'amount'      => $amount,
                'description' => $description,
                'selectable'  => $selectable,
                'bk_code'     => $list[$id]['bk_code'],
                'createdAt'   => $list[$id]['createdAt'],
            ];
        }

        return $reordered;
    }
}
