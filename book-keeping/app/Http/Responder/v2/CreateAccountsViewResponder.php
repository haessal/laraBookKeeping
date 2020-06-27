<?php

namespace App\Http\Responder\v2;

use Illuminate\Http\Response;

class CreateAccountsViewResponder extends BaseAccountsViewResponder
{
    /**
     * Respond the CreateAccountsView.
     *
     * @param array $context
     *
     * @return Illuminate\Http\Response
     */
    public function response(array $context): Response
    {
        $this->response->setContent($this->view->make('bookkeeping.v2.pageaccountscreate', [
            'dropdownmenuLinks' => $this->dropdownMenuLinks(),
            'book'              => $context['book'],
            'selflinkname'      => 'v2_accounts',
            'navilinks'         => $this->navilinks(),
            'accountsnavilinks' => ['list' => $this->accountsnavilinks(), 'selected' => 'create'],
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}