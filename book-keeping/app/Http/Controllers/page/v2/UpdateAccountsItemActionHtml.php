<?php

namespace App\Http\Controllers\page\v2;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\page\v2\UpdateAccountsItemViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UpdateAccountsItemActionHtml extends AuthenticatedBookKeepingAction
{
    /**
     * UpdateAccountsItemView responder instance.
     *
     * @var \App\Http\Responder\page\v2\UpdateAccountsItemViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\page\v2\UpdateAccountsItemViewResponder  $responder
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
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $bookId
     * @param  string  $accountsItemId
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, string $bookId, string $accountsItemId): Response
    {
        $context = [];

        if (! $this->BookKeeping->validateUuid($bookId)) {
            abort(Response::HTTP_NOT_FOUND);
        }
        if (! $this->BookKeeping->validateUuid($accountsItemId)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        [$status, $information] = $this->BookKeeping->retrieveBookInformation($bookId);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                if (isset($information)) {
                    $context['bookId'] = $bookId;
                    $context['book'] = $information;
                } else {
                    abort(Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;
            case BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE:
                abort(Response::HTTP_NOT_FOUND);
                break;
            default:
                abort(Response::HTTP_INTERNAL_SERVER_ERROR);
                break;
        }

        $context['message'] = null;
        if ($request->isMethod('post')) {
            $group = trim($request->input('accountgroup'));
            $title = trim($request->input('title'));
            $description = trim($request->input('description'));
            if (array_key_exists('attribute_selectable', $request->all())) {
                $selectable = true;
            } else {
                $selectable = false;
            }
            if (($group != '') && $this->BookKeeping->validateUuid($group)
                    && ($title != '') && ($description != '')) {
                $newData = ['group' => $group, 'title' => $title, 'description' => $description, 'selectable' => $selectable];
                [$status, $_] = $this->BookKeeping->updateAccount($accountsItemId, $newData, $bookId);
                switch ($status) {
                    case BookKeepingService::STATUS_NORMAL:
                        break;
                    case BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN:
                        $context['message'] = __('You are not permitted to write in this book.');
                        break;
                    case BookKeepingService::STATUS_ERROR_BAD_CONDITION:
                        abort(Response::HTTP_NOT_FOUND);
                        break;
                    default:
                        abort(Response::HTTP_INTERNAL_SERVER_ERROR);
                        break;
                }
            } else {
                $context['message']
                    = __('Please select the group and enter a valid name and description.');
            }
        }

        [$status, $categorizedAccounts] = $this->BookKeeping->retrieveCategorizedAccounts(false, $bookId);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                if (isset($categorizedAccounts)) {
                    $context['accounts'] = $categorizedAccounts;
                } else {
                    abort(Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;
            default:
                abort(Response::HTTP_INTERNAL_SERVER_ERROR);
                break;
        }
        $accountTypeCaption = [
            'asset'     => __('Assets'),
            'liability' => __('Liabilities'),
            'expense'   => __('Expense'),
            'revenue'   => __('Revenue'),
        ];
        $context['accountsitem'] = null;
        foreach ($categorizedAccounts as $accountTypeKey => $accountType) {
            foreach ($accountType['groups'] as $accountGroupKey => $accountGroup) {
                if (array_key_exists($accountsItemId, $accountGroup['items'])) {
                    $context['accounttypekey'] = $accountTypeKey;
                    $context['accountsitem']['id'] = $accountsItemId;
                    $context['accountsitem']['type'] = $accountTypeCaption[$accountTypeKey];
                    $context['accountsitem']['groupid'] = $accountGroupKey;
                    $accountItem = $accountGroup['items'][$accountsItemId];
                    $context['accountsitem']['title'] = $accountItem['title'];
                    $context['accountsitem']['description'] = $accountItem['description'];
                    $context['accountsitem']['attribute_selectable'] = $accountItem['selectable'] ? 'checked' : null;
                    $context['accountsitem']['bk_code'] = $accountItem['bk_code'];
                }
            }
        }

        return $this->responder->response($context);
    }
}
