<?php

namespace App\Http\Controllers\page\v1;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\page\v1\ShowStatementsViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShowStatementsActionHTML extends AuthenticatedBookKeepingAction
{
    /**
     * ShowStatementsView responder instance.
     *
     * @var \App\Http\Responder\page\v1\ShowStatementsViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\page\v1\ShowStatementsViewResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, ShowStatementsViewResponder $responder)
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

        $today = date('Y-m-d');
        if ($request->isMethod('post')) {
            $beginningDate = trim(strval($request->input('BEGINNING')));
            $endDate = trim(strval($request->input('END')));
        } else {
            $beginningDate = $today;
            $endDate = $today;
        }
        $context['beginning_date'] = $beginningDate;
        $context['end_date'] = $endDate;
        $context['profit_loss'] = null;
        $context['trial_balance'] = null;
        $context['previous_balance_sheet'] = null;
        $context['balance_sheet'] = null;
        $context['slips'] = null;
        if (! empty($beginningDate) && ! empty($endDate)
            && $this->BookKeeping->validatePeriod($beginningDate, $endDate)) {
            [$status, $statements]
                = $this->BookKeeping->retrieveProfitLossTrialBalanceBalanceSheetsSlips($beginningDate, $endDate);
            switch ($status) {
                case BookKeepingService::STATUS_NORMAL:
                    if (isset($statements)) {
                        $context['profit_loss'] = $statements['profit_loss'];
                        $context['trial_balance'] = $statements['trial_balance'];
                        $context['previous_balance_sheet'] = $statements['previous_balance_sheet'];
                        $context['balance_sheet'] = $statements['balance_sheet'];
                        $context['slips'] = $statements['slips'];
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
            $context['display_statements'] = true;
        } else {
            $message = __('There is no item to be shown.');
            $context['message'] = strval($message);
            $context['display_statements'] = false;
        }

        return $this->responder->response($context);
    }
}
