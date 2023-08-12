<?php

namespace App\Http\Responder\page\v2;

use Illuminate\Http\Response;

class ShowAccountsViewResponder extends BaseAccountsViewResponder
{
    /**
     * Respond the ShowAccountsView.
     *
     * @param  array  $context
     * @return Illuminate\Http\Response
     */
    public function response(array $context): Response
    {
        $accounts = $context['accounts'];
        $account_list = [
            'asset'     => $this->sortAccountInAscendingCodeOrder($accounts['asset']['groups']),
            'liability' => $this->sortAccountInAscendingCodeOrder($accounts['liability']['groups']),
            'expense'   => $this->sortAccountInAscendingCodeOrder($accounts['expense']['groups']),
            'revenue'   => $this->sortAccountInAscendingCodeOrder($accounts['revenue']['groups']),
        ];
        $this->response->setContent($this->view->make('bookkeeping.v2.pageaccountslist', [
            'dropdownmenuLinks' => $this->dropdownMenuLinks(),
            'book'              => $context['book'],
            'selflinkname'      => 'v2_accounts',
            'navilinks'         => $this->navilinks(),
            'accountsnavilinks' => ['list' => $this->accountsnavilinks(), 'selected' => 'list'],
            'accounts'          => $account_list,
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
