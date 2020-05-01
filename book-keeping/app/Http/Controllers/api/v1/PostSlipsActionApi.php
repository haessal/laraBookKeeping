<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\AuthenticatedBookKeepingAPIAction;
use App\Http\Responder\api\v1\SlipJsonResponder;
use App\Service\BookKeepingService;

class PostSlipsActionApi extends AuthenticatedBookKeepingAPIAction
{
    /**
     * SlipJson responder instance.
     *
     * @var \App\Http\Responder\api\v1\SlipJsonResponder
     */
    protected $responder;

    /**
     * Create a new controller instance.
     *
     * @param \App\Service\BookKeepingService              $BookKeeping
     * @param \App\Http\Responder\api\v1\SlipJsonResponder $responder
     *
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, SlipJsonResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

    protected function trimDraftSlip(array $slip_in): array
    {
        $slip_out = [];
        if (array_key_exists('outline', $slip_in)) {
            $slip_out['outline'] = trim($slip_in['outline']);
        }
        if (array_key_exists('date', $slip_in)) {
            $slip_out['date'] = trim($slip_in['date']);
        }
        $slip_out['entries'] = [];
        if (array_key_exists('entries', $slip_in)) {
            if (!empty($slip_in['entries'])) {
                foreach ($slip_in['entries'] as $slipEntry_in) {
                    $slipEntry_out = [];
                    if (array_key_exists('debit', $slipEntry_in)) {
                        $slipEntry_out['debit'] = trim($slipEntry_in['debit']);
                    }
                    if (array_key_exists('credit', $slipEntry_in)) {
                        $slipEntry_out['credit'] = trim($slipEntry_in['credit']);
                    }
                    if (array_key_exists('amount', $slipEntry_in)) {
                        $slipEntry_out['amount'] = $slipEntry_in['amount'];
                    }
                    if (array_key_exists('client', $slipEntry_in)) {
                        $slipEntry_out['client'] = trim($slipEntry_in['client']);
                    }
                    if (array_key_exists('outline', $slipEntry_in)) {
                        $slipEntry_out['outline'] = trim($slipEntry_in['outline']);
                    }
                    $slip_out['entries'][] = $slipEntry_out;
                }
            }
        }
        $slip_out['memo'] = null;
        if (array_key_exists('memo', $slip_in)) {
            $trim_memo = trim($slip_in['memo']);
            if (!empty($trim_memo)) {
                $slip_out['memo'] = $trim_memo;
            }
        }

        return $slip_out;
    }

    protected function validateDraftSlip(array $slip, array $accounts): bool
    {
        $success = true;

        if (!array_key_exists('outline', $slip) || empty($slip['outline'])) {
            $success = false;
        }
        if (!array_key_exists('date', $slip) || empty($slip['date'])) {
            $success = false;
        } else {
            if (!($this->validateDate($slip['date']))) {
                $success = false;
            }
        }
        if (!array_key_exists('entries', $slip) || empty($slip['entries']) || !is_array($slip['entries'])) {
            $success = false;
        } else {
            foreach ($slip['entries'] as $slipEntry) {
                if (!array_key_exists('debit', $slipEntry) || empty($slipEntry['debit']) || !array_key_exists($slipEntry['debit'], $accounts)) {
                    $success = false;
                }
                if (!array_key_exists('credit', $slipEntry) || empty($slipEntry['credit']) || !array_key_exists($slipEntry['credit'], $accounts)) {
                    $success = false;
                }
                if (!array_key_exists('amount', $slipEntry) || empty($slipEntry['amount']) || !is_numeric($slipEntry['amount'])) {
                    $success = false;
                }
                if (!array_key_exists('client', $slipEntry) || empty($slipEntry['client'])) {
                    $success = false;
                }
                if (!array_key_exists('outline', $slipEntry) || empty($slipEntry['outline'])) {
                    $success = false;
                }
            }
        }

        return $success;
    }

    private function validateDate(string $date)
    {
        return true;
    }
}
