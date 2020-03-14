<?php

namespace App\Http\Responder\v1;

use Illuminate\Http\Response;

class CreateSlipViewResponder extends BaseViewResponder
{
    /**
     * Response the Form to create new Slip.
     *
     * @param array $context
     *
     * @return Illuminate\Http\Response
     */
    public function response(array $context): Response
    {
        $addparameter = ['debit' => '', 'client' => '', 'outline' => '', 'credit' => '', 'amount' => ''];
        if (array_key_exists('add', $context)) {
            $addparameter = $context['add'];
        }

        $accounts = $context['accounts'];
        $selectable = $this->selectableAccount($accounts);
        $reordered = $this->reorderedAccount($selectable);
        $account_title_list = $this->itemSelectedAccount($reordered, 'account_title');
        if (empty($context['draftslip'])) {
            $draftslip = [];
        } else {
            $draftslip = $this->translateDraftSlipFormat($context['draftslip']);
        }
        $this->response->setContent($this->view->make('bookkeeping.v1.pageslip', [
            'navilinks'          => $this->navilinks(),
            'add'                => $addparameter,
            'account_title_list' => $account_title_list,
            'slipdate'           => $context['slipdate'],
            'draftslip'          => $draftslip,
            'totalamount'        => $context['totalamount'],
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }

    private function itemSelectedAccount(array $accounts, string $key): array
    {
        $itemSelectedAccounts = [];

        foreach ($accounts as $accountsId => $accountsItem) {
            $itemSelectedAccounts[$accountsId] = $accountsItem[$key];
        }

        return $itemSelectedAccounts;
    }

    private function reorderedAccount(array $accounts): array
    {
        $reordered = [];
        $structured = [
            'asset'     => ['isCurrent' => [], 'isNotCurrent' => []],
            'liability' => ['isCurrent' => [], 'isNotCurrent' => []],
            'expense'   => ['isCurrent' => [], 'isNotCurrent' => []],
            'revenue'   => ['isCurrent' => [], 'isNotCurrent' => []],
        ];

        foreach ($accounts as $accountsId => $accountsItem) {
            if ($accountsItem['is_current'] == true) {
                $structured[$accountsItem['account_type']]['isCurrent'][$accountsItem['account_group_id']]['account_group_bk_code'] = $accountsItem['account_group_bk_code'];
                $structured[$accountsItem['account_type']]['isCurrent'][$accountsItem['account_group_id']]['account_group_created_at'] = $accountsItem['account_group_created_at'];
                $structured[$accountsItem['account_type']]['isCurrent'][$accountsItem['account_group_id']]['account_items'][$accountsItem['account_id']] = $accountsItem;
            } else {
                $structured[$accountsItem['account_type']]['isNotCurrent'][$accountsItem['account_group_id']]['account_group_bk_code'] = $accountsItem['account_group_bk_code'];
                $structured[$accountsItem['account_type']]['isNotCurrent'][$accountsItem['account_group_id']]['account_group_created_at'] = $accountsItem['account_group_created_at'];
                $structured[$accountsItem['account_type']]['isNotCurrent'][$accountsItem['account_group_id']]['account_items'][$accountsItem['account_id']] = $accountsItem;
            }
        }
        foreach ($structured as $structuredId => $structuredItem) {
            foreach ($structuredItem as $isCurrentId => $isCurrentItem) {
                $structured[$structuredId][$isCurrentId] = $this->reorderedAccountGroupInAscendingCodeOrder($structured[$structuredId][$isCurrentId]);
            }
        }

        foreach ($structured as $typeKey => $separatedByTypeItem) {
            foreach ($separatedByTypeItem as $isCurrentKey => $isCurrentItem) {
                foreach ($isCurrentItem as $accountGroupId => $accountGroupItem) {
                    foreach ($accountGroupItem['account_items'] as $accountId => $accountItem) {
                        $reordered[$accountId] = $accountItem;
                    }
                }
            }
        }

        return $reordered;
    }

    private function reorderedAccountGroupInAscendingCodeOrder($accountGroups)
    {
        $sortedIds1 = [];
        $sortedIds2 = [];
        $reordered = [];

        foreach ($accountGroups as $accountGroupIds => $accountGroupItem) {
            if (!is_null($accountGroupItem['account_group_bk_code'])) {
                $sortedIds1[$accountGroupIds] = $accountGroupItem['account_group_bk_code'];
            } else {
                $sortedIds2[$accountGroupIds] = $accountGroupItem['account_group_created_at'];
            }
        }
        asort($sortedIds1);
        asort($sortedIds2);
        $sortedIds = $sortedIds1 + $sortedIds2;
        foreach ($sortedIds as $Ids => $item) {
            $reordered[$Ids]['account_group_bk_code'] = $accountGroups[$Ids]['account_group_bk_code'];
            $reordered[$Ids]['account_group_created_at'] = $accountGroups[$Ids]['account_group_created_at'];
            $reordered[$Ids]['account_items'] = $this->reorderedAccountInAscendingCodeOrder($accountGroups[$Ids]['account_items']);
        }

        return $reordered;
    }

    private function reorderedAccountInAscendingCodeOrder($accounts)
    {
        $sortedIds1 = [];
        $sortedIds2 = [];
        $reordered = [];

        foreach ($accounts as $accountIds => $accountItem) {
            if (!is_null($accountItem['bk_code'])) {
                $sortedIds1[$accountIds] = $accountItem['bk_code'];
            } else {
                $sortedIds2[$accountIds] = $accountItem['created_at'];
            }
        }
        asort($sortedIds1);
        asort($sortedIds2);
        $sortedIds = $sortedIds1 + $sortedIds2;
        foreach ($sortedIds as $Ids => $item) {
            $reordered[$Ids] = $accounts[$Ids];
        }

        return $reordered;
    }

    private function selectableAccount(array $accounts): array
    {
        $selectable = [];
        foreach ($accounts as $accountsId => $accountsItem) {
            if ($accountsItem['selectable'] == true) {
                $selectable[$accountsId] = $accountsItem;
            }
        }

        return $selectable;
    }

    private function translateDraftSlipFormat(array $slips): array
    {
        $formatted = [];
        $slipId = key($slips);
        $trclass = 'evn';
        foreach ($slips[$slipId]['items'] as $slipEntryId => $slipEntryItem) {
            $formatted[$slipEntryId] = [
                'no'      => substr($slipEntryId, 0, 6).'..',
                'debit'   => $slipEntryItem['debit']['account_title'],
                'client'  => $slipEntryItem['client'],
                'outline' => $slipEntryItem['outline'],
                'credit'  => $slipEntryItem['credit']['account_title'],
                'amount'  => $slipEntryItem['amount'],
                'trclass' => $trclass,
            ];
            if ($trclass == 'evn') {
                $trclass = 'odd';
            } else {
                $trclass = 'evn';
            }
        }

        return $formatted;
    }
}
