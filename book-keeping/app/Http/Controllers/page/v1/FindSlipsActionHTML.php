<?php

namespace App\Http\Controllers\page\v1;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Requests\page\v1\FindSlipsRequest;
use App\Http\Responder\page\v1\FindSlipsViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Response;

class FindSlipsActionHTML extends AuthenticatedBookKeepingAction
{
    /**
     * FindSlipsViewResponder responder instance.
     *
     * @var \App\Http\Responder\page\v1\FindSlipsViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\page\v1\FindSlipsViewResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, FindSlipsViewResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \App\Http\Requests\page\v1\FindSlipsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(FindSlipsRequest $request): Response
    {
        $context = [];
        $beginningDate = null;
        $endDate = null;
        $debit = null;
        $credit = null;
        $andOr = null;
        $keyword = null;
        $slips = [];
        $message = __('There is no condition for search.');

        [$status, $categorizedAccounts] = $this->BookKeeping->retrieveCategorizedAccounts(true);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                if (isset($categorizedAccounts)) {
                    $context['accounts'] = $categorizedAccounts;
                } else {
                    abort(Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                break;
            case BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE:
                abort(Response::HTTP_NOT_FOUND);
            default:
                abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        if ($request->isMethod('post')) {
            $buttonAction = $request->button_action();
            if ($buttonAction == 'delete') {
                $selectedSlipEntries = $request->slip_entry_ids();
                foreach ($selectedSlipEntries as $slipEntryId) {
                    $this->BookKeeping->deleteSlipEntryAndEmptySlip($slipEntryId);
                }
            }
            $beginningDate = $request->beginning_date();
            $endDate = $request->end_date();
            $debit = $request->debit();
            $credit = $request->credit();
            $andOr = $request->and_or();
            $keyword = $request->keyword();
            if (! empty($beginningDate) || ! empty($endDate) || ! empty($debit) || ! empty($credit) || ! empty($keyword)) {
                if ($this->BookKeeping->validatePeriod($beginningDate, $endDate)) {
                    [$status, $slips] = $this->BookKeeping->retrieveSlips(
                        $beginningDate, $endDate, $debit, $credit, $andOr, $keyword
                    );
                    switch ($status) {
                        case BookKeepingService::STATUS_NORMAL:
                            if (isset($slips)) {
                                $message = null;
                                if (empty($slips)) {
                                    $message = __('No items that match the condition.');
                                }
                            } else {
                                abort(Response::HTTP_INTERNAL_SERVER_ERROR);
                            }
                            break;
                        default:
                            abort(Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                } else {
                    $message = __('Invalid date format.');
                }
            }
        }
        $context['beginning_date'] = $beginningDate;
        $context['end_date'] = $endDate;
        $context['debit'] = $debit;
        $context['credit'] = $credit;
        $context['and_or'] = $andOr;
        $context['keyword'] = $keyword;
        $context['slips'] = $slips;
        $context['message'] = is_string($message) ? $message : '';

        return $this->responder->response($context);
    }
}
