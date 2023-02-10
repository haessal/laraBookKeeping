<?php

namespace App\Http\Responder\v2;

use Illuminate\Http\Response;

class CreateAccountsViewResponder extends BaseAccountsViewResponder
{
    /**
     * Respond the CreateAccountsView.
     *
     * @param  array  $context
     * @return Illuminate\Http\Response
     */
    public function response(array $context): Response
    {
        $accounts = $context['accounts'];
        $accounts_title = $this->translateAccountListToTitleList(
            [
                'asset'     => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['asset']['groups'])],
                'liability' => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['liability']['groups'])],
                'expense'   => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['expense']['groups'])],
                'revenue'   => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['revenue']['groups'])],
            ],
            true
        );
        $accounttype = ['asset' => null, 'liability' => null, 'expense' => null, 'revenue' => null];
        if (! empty($context['accounttype'])) {
            $accounttype[$context['accounttype']] = 'checked';
        }
        $this->response->setContent($this->view->make('bookkeeping.v2.pageaccountscreate', [
            'dropdownmenuLinks' => $this->dropdownMenuLinks(),
            'book'              => $context['book'],
            'selflinkname'      => 'v2_accounts',
            'navilinks'         => $this->navilinks(),
            'accountsnavilinks' => ['list' => $this->accountsnavilinks(), 'selected' => 'create'],
            'accounttype'       => $accounttype,
            'accountstitle'     => $accounts_title['groupsWithType'],
            'accountcreate'     => $context['accountcreate'],
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
