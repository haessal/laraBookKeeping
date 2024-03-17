<?php

namespace App\Http\Controllers\page\v1;

use App\Http\Controllers\AuthenticatedBookKeepingAction;
use App\Http\Responder\page\v1\CreateSlipViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class CreateSlipActionHTML extends AuthenticatedBookKeepingAction
{
    /**
     * CreateSlipView responder instance.
     *
     * @var \App\Http\Responder\page\v1\CreateSlipViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\page\v1\CreateSlipViewResponder  $responder
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request): Response
    {
        $context = [];

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
        $date = trim(strval($request->input('date')));
        $context['add'] = null;
        if ($request->isMethod('post')) {
            if (is_array($request->input('buttons'))) {
                $buttonAction = strval(key($request->input('buttons')));
            } else {
                $buttonAction = '';
            }
            switch ($buttonAction) {
                case 'add':
                    $addDebit = trim(strval($request->input('debit')));
                    $addClient = trim(strval($request->input('client')));
                    $addOutline = trim(strval($request->input('outline')));
                    $addCredit = trim(strval($request->input('credit')));
                    $addAmount = intval($request->input('amount'));
                    if ($this->validateForCreateSlipEntry(
                        $addDebit, $addClient, $addOutline, $addCredit, $addAmount
                    )) {
                        $this->BookKeeping->createSlipEntryAsDraft(
                            $addDebit, $addClient, $addOutline, $addCredit, $addAmount
                        );
                    }
                    $context['add'] = [
                        'debit' => $addDebit,
                        'client' => $addClient,
                        'outline' => $addOutline,
                        'credit' => $addCredit,
                        'amount' => $addAmount,
                    ];
                    break;
                case 'delete':
                    $slipEntryId = $request->input('modify_no');
                    if (isset($slipEntryId)) {
                        $this->BookKeeping->deleteSlipEntryAndEmptySlip(trim(strval($slipEntryId)));
                    }
                    break;
                case 'submit':
                    if ($this->BookKeeping->validateDateFormat($date)) {
                        $this->BookKeeping->submitDraftSlip($date);
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
        [$status, $draftSlips] = $this->BookKeeping->retrieveDraftSlips();
        if (($status == BookKeepingService::STATUS_NORMAL) && (isset($draftSlips))) {
            $totalamount = 0;
            if (! empty($draftSlips)) {
                $firstDraftSlip = reset($draftSlips);
                foreach ($firstDraftSlip['items'] as $draftslipItem) {
                    $totalamount += intval($draftslipItem['amount']);
                }
            }
            $context['totalamount'] = $totalamount;
            $context['draftslip'] = $draftSlips;
        } else {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->responder->response($context);
    }

    /**
     * Validate arguments for create SlipEntry.
     *
     * @param  string  $debit
     * @param  string  $client
     * @param  string  $outline
     * @param  string  $credit
     * @param  int  $amount
     * @return bool
     */
    private function validateForCreateSlipEntry(string $debit, string $client, string $outline, string $credit, int $amount): bool
    {
        $success = false;
        if (! ($debit === '0') && ! ($credit === '0') && ($debit != $credit)
            && ! empty($client) && ! empty($outline) && ($amount != 0)) {
            $success = true;
        }

        return $success;
    }
}
