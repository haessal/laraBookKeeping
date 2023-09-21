<?php

namespace App\Http\Responder\page\v2;

use Illuminate\Http\Response;

class UpdateAccountsGroupViewResponder extends BaseAccountsViewResponder
{
    /**
     * Respond the UpdateAccountsGroupView.
     *
     * @param  array  $context
     * @return \Illuminate\Http\Response
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
        $this->response->setContent($this->view->make('bookkeeping.v2.pageaccountssettings', [
            'bookId'           => $context['bookId'],
            'book'             => $context['book'],
            'selflinkname'     => 'v2_accounts',
            'selfaccountsmenu' => 'accounts_settings',
            'accountstitle'    => $accounts_title,
            'accountsgroup'    => $context['accountsgroup'],
            'message'          => $context['message'],
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
