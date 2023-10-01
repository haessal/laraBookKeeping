<?php

namespace App\Http\Responder\page\v1;

use Illuminate\Http\Response;

class CreateSlipViewResponder extends BaseViewResponder
{
    /**
     * Response the Form to create new Slip.
     *
     * @param  array{
     *   accounts: array<string, array{
     *     groups:array<string, array{
     *       title: string,
     *       isCurrent: bool,
     *       bk_code: int,
     *       createdAt: string,
     *       items: array<string, array{
     *         title: string,
     *         description: string,
     *         selectable: bool,
     *         bk_code: int,
     *         createdAt: string,
     *       }>
     *     }>|array{}
     *   }>,
     *   add: array{
     *     debit: string,
     *     client: string,
     *     outline: string,
     *     credit: string,
     *     amount: int,
     *   }|null,
     *   slipdate: string,
     *   totalamount: int,
     *   draftslip: array<string, array{
     *     date: string,
     *     slip_outline: string,
     *     slip_memo: string,
     *     items: array<string, array{
     *       debit: array{account_id: string, account_title: string},
     *       credit: array{account_id: string, account_title: string},
     *       amount: int,
     *       client: string,
     *       outline: string,
     *     }>
     *   }>,
     * }  $context
     * @return \Illuminate\Http\Response
     */
    public function response(array $context): Response
    {
        $addparameter = ['debit' => '', 'client' => '', 'outline' => '', 'credit' => '', 'amount' => ''];
        if (isset($context['add'])) {
            $addparameter = $context['add'];
        }

        $accounts = $context['accounts'];
        $account_title_list = $this->translateAccountListToTitleList(
            [
                'asset'     => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['asset']['groups'])],
                'liability' => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['liability']['groups'])],
                'expense'   => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['expense']['groups'])],
                'revenue'   => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['revenue']['groups'])],
            ]
        );
        $draftslip = [];
        if (! empty($context['draftslip'])) {
            $draftslip = $this->translateDraftSlipFormat($context['draftslip']);
        }
        $this->response->setContent($this->view->make('bookkeeping.v1.pageslip', [
            'navigation'         => $this->navigationList(),
            'add'                => $addparameter,
            'account_title_list' => $account_title_list,
            'slipdate'           => $context['slipdate'],
            'draftslip'          => $draftslip,
            'totalamount'        => $context['totalamount'],
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
