<?php

namespace App\Http\Controllers\page\v2;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\page\v2\UpdateAccountsGroupViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UpdateAccountsGroupActionHtml extends AuthenticatedBookKeepingAction
{
    /**
     * UpdateAccountsGroupView responder instance.
     *
     * @var \App\Http\Responder\page\v2\UpdateAccountsGroupViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\page\v2\UpdateAccountsGroupViewResponder  $responder
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
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $bookId
     * @param  string  $accountsGroupId
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, string $bookId, string $accountsGroupId): Response
    {
        $context = [];

        if (! $this->BookKeeping->validateUuid($bookId)) {
            abort(Response::HTTP_NOT_FOUND);
        }
        if (! $this->BookKeeping->validateUuid($accountsGroupId)) {
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
            default:
                abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $context['message'] = null;
        if ($request->isMethod('post')) {
            $title = trim(strval($request->input('title')));
            if (array_key_exists('attribute_current', $request->all())) {
                $is_current = true;
            } else {
                $is_current = false;
            }
            if ($title != '') {
                $newData = ['title' => $title, 'is_current' => $is_current];
                [$status, $_] = $this->BookKeeping->updateAccountGroup($accountsGroupId, $newData, $bookId);
                switch ($status) {
                    case BookKeepingService::STATUS_NORMAL:
                        break;
                    case BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN:
                        $context['message'] = __('You are not permitted to write in this book.');
                        break;
                    case BookKeepingService::STATUS_ERROR_BAD_CONDITION:
                        abort(Response::HTTP_NOT_FOUND);
                    default:
                        abort(Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $context['message'] = __('Please enter a valid name.');
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
        }
        $accountTypeCaption = [
            'asset'     => __('Assets'),
            'liability' => __('Liabilities'),
            'expense'   => __('Expense'),
            'revenue'   => __('Revenue'),
        ];
        $context['accountsgroup'] = null;
        foreach ($categorizedAccounts as $accountTypeKey => $accountType) {
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
