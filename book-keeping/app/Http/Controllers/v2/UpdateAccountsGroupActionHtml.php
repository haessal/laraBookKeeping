<?php

namespace App\Http\Controllers\v2;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\v2\UpdateAccountsGroupViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UpdateAccountsGroupActionHtml extends AuthenticatedBookKeepingAction
{
    /**
     * UpdateAccountsGroupView responder instance.
     *
     * @var \App\Http\Responder\v2\UpdateAccountsGroupViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param \App\Service\BookKeepingService                         $BookKeeping
     * @param \App\Http\Responder\v2\UpdateAccountsGroupViewResponder $responder
     *
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, UpdateAccountsGroupViewResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $bookId
     * @param string                   $accountsGroupId
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, string $bookId, string $accountsGroupId): Response
    {
        $context = [];

        $accountTypeCaption = [
            'asset'     => __('Assets'),
            'liability' => __('Liabilities'),
            'expense'   => __('Expense'),
            'revenue'   => __('Revenue'),
        ];
        if ($request->isMethod('post')) {
            $title = trim($request->input('title'));
            if (array_key_exists('attribute_current', $request->all())) {
                $is_current = true;
            } else {
                $is_current = false;
            }
            $newData = ['title' => $title, 'is_current' => $is_current];
            $this->BookKeeping->updateAccountGroup($accountsGroupId, $newData, $bookId);
        }
        $context['book'] = $this->BookKeeping->retrieveBookInformation($bookId);
        $context['accounts'] = $this->BookKeeping->retrieveAccounts(false, $bookId);
        $accounts = $context['accounts'];
        $context['accountsgroup'] = null;
        foreach ($accounts as $accountTypeKey => $accountType) {
            if (array_key_exists($accountsGroupId, $accountType['groups'])) {
                $context['accountsgroup']['id'] = $accountsGroupId;
                $context['accountsgroup']['type'] = $accountTypeCaption[$accountTypeKey];
                $context['accountsgroup']['title'] = $accountType['groups'][$accountsGroupId]['title'];
                $context['accountsgroup']['attribute_current'] = $accountType['groups'][$accountsGroupId]['isCurrent'] ? 'checked' : null;
                $context['accountsgroup']['bk_code'] = $accountType['groups'][$accountsGroupId]['bk_code'];
            }
        }

        return $this->responder->response($context);
    }
}
