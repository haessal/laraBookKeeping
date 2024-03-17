<?php

namespace App\Http\Responder\page\v2;

use Illuminate\Http\Response;

class ShowAccountsViewResponder extends BaseAccountsViewResponder
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
        $account_list = [
            'asset' => $this->sortAccountInAscendingCodeOrder($accounts['asset']['groups']),
            'liability' => $this->sortAccountInAscendingCodeOrder($accounts['liability']['groups']),
            'expense' => $this->sortAccountInAscendingCodeOrder($accounts['expense']['groups']),
            'revenue' => $this->sortAccountInAscendingCodeOrder($accounts['revenue']['groups']),
        ];
        $this->response->setContent($this->view->make('bookkeeping.v2.pageaccountslist', [
            'bookId' => $context['bookId'],
            'book' => $context['book'],
            'selflinkname' => 'v2_accounts',
            'selfaccountsmenu' => 'accounts_list',
            'accounts' => $account_list,
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
