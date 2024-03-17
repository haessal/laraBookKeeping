<?php

namespace App\Http\Responder\page\v2;

use Illuminate\Http\Response;

class UpdateAccountsItemViewResponder extends BaseAccountsViewResponder
{
    /**
     * Respond the UpdateAccountsItemView.
     *
     * @param  array{
     *   bookId: string,
     *   book: array{
     *     id: string,
     *     owner: string,
     *     name: string,
     *   },
     *   message: string|null,
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
     *   accountsitem: array{
     *     id: string,
     *     type: string,
     *     groupId: string,
     *     title: string,
     *     description: string,
     *     attribute_selectable: 'checked'|null,
     *     bk_code: int,
     *   }|null,
     *   accounttypekey: string,
     * }  $context
     * @return \Illuminate\Http\Response
     */
    public function response(array $context): Response
    {
        $accounts = $context['accounts'];
        $accounts_sorted = [
            'asset' => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['asset']['groups'])],
            'liability' => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['liability']['groups'])],
            'expense' => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['expense']['groups'])],
            'revenue' => ['groups' => $this->sortAccountInAscendingCodeOrder($accounts['revenue']['groups'])],
        ];
        $accounts_title = $this->translateAccountListToTitleList($accounts_sorted, true);
        $accounts_groups = $this->translateAccountListToTitleList([
            $context['accounttypekey'] => ['groups' => $accounts_sorted[$context['accounttypekey']]['groups']],
        ], true);
        $this->response->setContent($this->view->make('bookkeeping.v2.pageaccountssettings', [
            'bookId' => $context['bookId'],
            'book' => $context['book'],
            'selflinkname' => 'v2_accounts',
            'selfaccountsmenu' => 'accounts_settings',
            'accountstitle' => $accounts_title,
            'accountsitem' => $context['accountsitem'],
            'accountsgroups' => $accounts_groups['groups'],
            'message' => $context['message'],
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
