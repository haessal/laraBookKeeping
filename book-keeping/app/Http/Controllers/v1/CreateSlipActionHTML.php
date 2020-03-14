<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\v1\CreateSlipViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class CreateSlipActionHTML extends AuthenticatedBookKeepingAction
{
    /**
     * CreateSlipView responder instance.
     *
     * @var \App\Http\Responder\v1\CreateSlipViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param \App\Service\BookKeepingService                $BookKeeping
     * @param \App\Http\Responder\v1\CreateSlipViewResponder $responder
     *
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, CreateSlipViewResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request): Response
    {
        $context = [];

        $accounts = $this->BookKeeping->retrieveAccounts();
        $context['accounts'] = $accounts;
        $date = trim($request->input('date'));

        if ($request->isMethod('post')) {
            $button_action = key($request->input('buttons'));
            switch ($button_action) {
                case 'add':
                    $add_debit = $request->input('debit');
                    $add_client = trim($request->input('client'));
                    $add_outline = trim($request->input('outline'));
                    $add_credit = $request->input('credit');
                    $add_amount = intval(trim($request->input('amount')));
                    $this->BookKeeping->createSlipEntryAsDraft($add_debit, $add_client, $add_outline, $add_credit, $add_amount);
                    $context['add'] = [
                        'debit'   => $add_debit,
                        'client'  => $add_client,
                        'outline' => $add_outline,
                        'credit'  => $add_credit,
                        'amount'  => $add_amount,
                    ];
                    break;
                case 'delete':
                    $slipEntryId = $request->input('modifyno');
                    if (!is_null($slipEntryId)) {
                        $this->BookKeeping->deleteSlipEntryAsDraft($slipEntryId);
                    }
                    break;
                case 'submit':
                    if (strptime($date, '%Y-%m-%d')) {
                        list($Y, $m, $d) = explode('-', $date);
                        if (checkdate($m, $d, $Y) === true) {
                            $this->BookKeeping->submitDraftSlip($date);
                        }
                    }
                    break;
                default:
                    break;
            }
        }

        if (empty($date)) {
            $today = new Carbon();
            $date = $today->format('Y-m-d');
        }
        $context['slipdate'] = $date;
        $context['draftslip'] = $this->BookKeeping->retrieveDraftSlips();

        $totalamount = 0;
        if (!empty($context['draftslip'])) {
            foreach ($context['draftslip'][key($context['draftslip'])]['items'] as $draftslipItem) {
                $totalamount += $draftslipItem['amount'];
            }
        }
        $context['totalamount'] = $totalamount;

        return $this->responder->response($context);
    }
}
