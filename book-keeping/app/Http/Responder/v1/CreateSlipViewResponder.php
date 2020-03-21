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
        $account_title_list = $this->translateAccountListFormat(
            [
                'asset'     => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['asset']['groups'])],
                'liability' => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['liability']['groups'])],
                'expense'   => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['expense']['groups'])],
                'revenue'   => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['revenue']['groups'])],
            ]
        );
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

    public function translateAccountListFormat(array $accounts): array
    {
        $account_title_list = [];

        foreach ($accounts as $accountType) {
            foreach ($accountType['groups'] as $accountGroupItem) {
                foreach ($accountGroupItem['items'] as $accountId => $accountItem) {
                    $account_title_list[$accountId] = $accountItem['account_title'];
                }
            }
        }

        return $account_title_list;
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
