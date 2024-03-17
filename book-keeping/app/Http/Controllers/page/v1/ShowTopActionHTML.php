<?php

namespace App\Http\Controllers\page\v1;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\page\v1\ShowTopViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShowTopActionHTML extends AuthenticatedBookKeepingAction
{
    /**
     * ShowTopView responder instance.
     *
     * @var \App\Http\Responder\page\v1\ShowTopViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\page\v1\ShowTopViewResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, ShowTopViewResponder $responder)
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
        $response = null;

        $today = date('Y-m-d');
        [$status, $statements] = $this->BookKeeping->retrieveProfitLossBalanceSheetSlipsOfOneDay($today);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                if (isset($statements)) {
                    $context['date'] = $today;
                    $context['income_statement'] = $statements['profit_loss'];
                    $context['balance_sheet'] = $statements['balance_sheet'];
                    $context['slips'] = $statements['slips'];
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
