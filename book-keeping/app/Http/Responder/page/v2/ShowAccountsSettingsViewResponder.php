<?php

namespace App\Http\Responder\page\v2;

use Illuminate\Http\Response;

class ShowAccountsSettingsViewResponder extends BaseAccountsViewResponder
{
    /**
     * Respond the ShowAccountsView.
     *
     * @param  array{
     *   bookId: string,
     *   book: array{
     *     id: string,
     *     owner: string,
     *     name: string,
     *   },
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
     *       }>,
     *     }>|array{},
     *   }>,
     * }  $context
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
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
