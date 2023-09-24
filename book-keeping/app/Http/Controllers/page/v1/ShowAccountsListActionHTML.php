<?php

namespace App\Http\Controllers\page\v1;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\page\v1\ShowAccountsListViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShowAccountsListActionHTML extends AuthenticatedBookKeepingAction
{
    /**
     * ShowAccountsListView responder instance.
     *
     * @var \App\Http\Responder\page\v1\ShowAccountsListViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\page\v1\ShowAccountsListViewResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, ShowAccountsListViewResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request): Response
    {
        $context = [];

        [$status, $categorizedAccounts] = $this->BookKeeping->retrieveCategorizedAccounts(false);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                if (isset($categorizedAccounts)) {
                    $context['accounts'] = $categorizedAccounts;
                    $response = $this->responder->response($context);
                }
                break;
            case BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE:
                abort(Response::HTTP_NOT_FOUND);
            default:
                break;
        }
        if (is_null($response)) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }
}
