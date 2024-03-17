<?php

namespace App\Http\Responder\page\v2;

use Illuminate\Http\Response;

class CreateAccountsViewResponder extends BaseAccountsViewResponder
{
    /**
     * Respond the CreateAccountsView.
     *
     * @param  array{
     *   bookId: string,
     *   book: array{
     *     id: string,
     *     owner: string,
     *     name: string,
     *   },
     *   accounttype: string|null,
     *   accountcreate: array{
     *      grouptitle: string|null,
     *      groupId: string|null,
     *      itemtitle: string|null,
     *      description: string|null,
     *   },
     *   messages: array{
     *     group: string|null,
     *     item: string|null,
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
                'asset' => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['asset']['groups'])],
                'liability' => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['liability']['groups'])],
                'expense' => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['expense']['groups'])],
                'revenue' => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['revenue']['groups'])],
            ],
            true
        );
        $accounttype = ['asset' => null, 'liability' => null, 'expense' => null, 'revenue' => null];
        if (! empty($context['accounttype'])) {
            $accounttype[$context['accounttype']] = 'checked';
        }
        $this->response->setContent($this->view->make('bookkeeping.v2.pageaccountscreate', [
            'bookId' => $context['bookId'],
            'book' => $context['book'],
            'selflinkname' => 'v2_accounts',
            'selfaccountsmenu' => 'accounts_add',
            'accounttype' => $accounttype,
            'accountstitle' => $accounts_title['groupsWithType'],
            'accountcreate' => $context['accountcreate'],
            'messages' => $context['messages'],
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
