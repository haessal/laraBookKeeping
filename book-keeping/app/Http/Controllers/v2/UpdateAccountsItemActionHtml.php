<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\v2\UpdateAccountsItemViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UpdateAccountsItemActionHtml extends AuthenticatedBookKeepingAction
{
    /**
     * UpdateAccountsItemView responder instance.
     *
     * @var \App\Http\Responder\v2\UpdateAccountsItemViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param \App\Service\BookKeepingService                        $BookKeeping
     * @param \App\Http\Responder\v2\UpdateAccountsItemViewResponder $responder
     *
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, UpdateAccountsItemViewResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $bookId
     * @param string                   $accountsItemId
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, string $bookId, string $accountsItemId): Response
    {
        $context = [];

        if ($request->isMethod('post')) {
            $group = trim($request->input('accountgroup'));
            $title = trim($request->input('title'));
            $description = trim($request->input('description'));
            if (array_key_exists('attribute_selectable', $request->all())) {
                $selectable = true;
            } else {
                $selectable = false;
            }
            $newData = ['group' => $group, 'title' => $title, 'description' => $description, 'selectable' => $selectable];
            $this->BookKeeping->updateAccount($bookId, $accountsItemId, $newData);
        }
        $context['book'] = $this->BookKeeping->retrieveBookInfomation($bookId);
        $context['accounts'] = $this->BookKeeping->retrieveAccounts(false, $bookId);
        $accounts = $context['accounts'];
        $context['accountsitem'] = null;
        foreach ($accounts as $accountTypeKey => $accountType) {
            foreach ($accountType['groups'] as $accountGroupKey => $accountGroup) {
                if (array_key_exists($accountsItemId, $accountGroup['items'])) {
                    $context['accountsitem']['id'] = $accountsItemId;
                    $context['accounttypekey'] = $accountTypeKey;
                    switch ($accountTypeKey) {
                        case 'asset':
                            $context['accountsitem']['type'] = __('Assets');
                            break;
                        case 'liability':
                            $context['accountsitem']['type'] = __('Liabilities');
                            break;
                        case 'expense':
                            $context['accountsitem']['type'] = __('Expense');
                            break;
                        case 'revenue':
                            $context['accountsitem']['type'] = __('Revenue');
                            break;
                        default:
                            $context['accountsitem']['type'] = null;
                            break;
                    }
                    $accountItem = $accountGroup['items'][$accountsItemId];
                    $context['accountsitem']['groupid'] = $accountGroupKey;
                    $context['accountsitem']['title'] = $accountItem['title'];
                    $context['accountsitem']['description'] = $accountItem['description'];
                    $context['accountsitem']['attribute_selectable'] = $accountItem['selectable'] ? 'checked' : null;
                    $context['accountsitem']['bk_code'] = $accountItem['bk_code'] ;
                }
            }
        }

        return $this->responder->response($context);
    }
}
