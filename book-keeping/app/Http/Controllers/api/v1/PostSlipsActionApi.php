<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Http\Responder\api\v1\SlipJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostSlipsActionApi extends AuthenticatedBookKeepingActionApi
{
    /**
     * SlipJson responder instance.
     *
     * @var \App\Http\Responder\api\v1\SlipJsonResponder
     */
    private $responder;

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

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $context = [];
        $accounts = $this->BookKeeping->retrieveAccountsList();
        $result = $this->validateAndTrimDraftSlip($request->all(), $accounts);
        if ($result['success']) {
            $slip = $result['slip'];
            $slipId = $this->BookKeeping->createSlip($slip['outline'], $slip['date'], $slip['entries'], $slip['memo']);
            $context['slips'] = $this->BookKeeping->retrieveSlip($slipId);
            $response = $this->responder->response($context);
        } else {
            $response = new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        return $response;
    }

    /**
     * Trim string data in Slip.
     *
     * @param array $slip_in
     *
     * @return array
     */
    private function trimDraftSlip(array $slip_in): array
    {
        $slip_out = [];
        if (array_key_exists('outline', $slip_in) && is_string($slip_in['outline'])) {
            $slip_out['outline'] = trim($slip_in['outline']);
        }
        if (array_key_exists('date', $slip_in) && is_string($slip_in['date'])) {
            $slip_out['date'] = trim($slip_in['date']);
        }
        $slip_out['entries'] = [];
        if (array_key_exists('entries', $slip_in)) {
            if (!empty($slip_in['entries']) && is_array($slip_in['entries'])) {
                foreach ($slip_in['entries'] as $slipEntry_in) {
                    if (!empty($slipEntry_in) && is_array($slipEntry_in)) {
                        $slipEntry_out = [];
                        if (array_key_exists('debit', $slipEntry_in) && is_string($slipEntry_in['debit'])) {
                            $slipEntry_out['debit'] = trim($slipEntry_in['debit']);
                        }
                        if (array_key_exists('credit', $slipEntry_in) && is_string($slipEntry_in['credit'])) {
                            $slipEntry_out['credit'] = trim($slipEntry_in['credit']);
                        }
                        if (array_key_exists('amount', $slipEntry_in)) {
                            $slipEntry_out['amount'] = $slipEntry_in['amount'];
                        }
                        if (array_key_exists('client', $slipEntry_in) && is_string($slipEntry_in['client'])) {
                            $slipEntry_out['client'] = trim($slipEntry_in['client']);
                        }
                        if (array_key_exists('outline', $slipEntry_in) && is_string($slipEntry_in['outline'])) {
                            $slipEntry_out['outline'] = trim($slipEntry_in['outline']);
                        }
                        if (!empty($slipEntry_out)) {
                            $slip_out['entries'][] = $slipEntry_out;
                        }
                    }
                }
            }
        }
        $slip_out['memo'] = null;
        if (array_key_exists('memo', $slip_in) && is_string($slip_in['memo'])) {
            $trim_memo = trim($slip_in['memo']);
            if (!empty($trim_memo)) {
                $slip_out['memo'] = $trim_memo;
            }
        }

        return $slip_out;
    }

    /**
     * Validate draft slip and trim string data.
     *
     * @param array $slip
     * @param array $accounts
     *
     * @return array
     */
    private function validateAndTrimDraftSlip(array $slip_in, array $accounts): array
    {
        $success = true;
        $slip_out = [];

        if (!array_key_exists('outline', $slip_in) || empty($slip_in['outline']) || !is_string($slip_in['outline'])) {
            $success = false;
        } else {
            $slip_out['outline'] = trim($slip_in['outline']);
        }
        if (!array_key_exists('date', $slip_in) || empty($slip_in['date']) || !is_string($slip_in['date'])) {
            $success = false;
        } else {
            $trim_date = trim($slip_in['date']);
            if (!($this->BookKeeping->validateDateFormat($trim_date))) {
                $success = false;
            } else {
                $slip_out['date'] = $trim_date;
            }
        }
        if (!array_key_exists('entries', $slip_in) || empty($slip_in['entries'])) {
            $success = false;
        } else {
            if (!is_array($slip_in['entries'])) {
                $success = false;
            } else {
                $slip_out['entries'] = [];
                foreach ($slip_in['entries'] as $slipEntry_in) {
                    if (empty($slipEntry_in) || !is_array($slipEntry_in)) {
                        $success = false;
                    } else {
                        $slipEntry_out = [];
                        if (!array_key_exists('debit', $slipEntry_in) || empty($slipEntry_in['debit']) || !is_string($slipEntry_in['debit'])) {
                            $success = false;
                        } else {
                            $trim_debit = trim($slipEntry_in['debit']);
                            if (!array_key_exists($trim_debit, $accounts)) {
                                $success = false;
                            } else {
                                $slipEntry_out['debit'] = $trim_debit;
                            }
                        }
                        if (!array_key_exists('credit', $slipEntry_in) || empty($slipEntry_in['credit']) || !is_string($slipEntry_in['credit'])) {
                            $success = false;
                        } else {
                            $trim_credit = trim($slipEntry_in['credit']);
                            if (!array_key_exists($trim_credit, $accounts)) {
                                $success = false;
                            } else {
                                $slipEntry_out['credit'] = $trim_credit;
                            }
                        }
                        if (!array_key_exists('amount', $slipEntry_in) || empty($slipEntry_in['amount']) || !is_numeric($slipEntry_in['amount'])) {
                            $success = false;
                        } else {
                            $slipEntry_out['amount'] = $slipEntry_in['amount'];
                        }
                        if (!array_key_exists('client', $slipEntry_in) || empty($slipEntry_in['client']) || !is_string($slipEntry_in['client'])) {
                            $success = false;
                        } else {
                            $slipEntry_out['client'] = trim($slipEntry_in['client']);
                        }
                        if (!array_key_exists('outline', $slipEntry_in) || empty($slipEntry_in['outline']) || !is_string($slipEntry_in['outline'])) {
                            $success = false;
                        } else {
                            $slipEntry_out['outline'] = trim($slipEntry_in['outline']);
                        }
                        if (!empty($slipEntry_out)) {
                            $slip_out['entries'][] = $slipEntry_out;
                        }
                    }
                }
            }
        }
        if (!array_key_exists('memo', $slip_in) || empty($slip_in['memo'])) {
            $slip_out['memo'] = null;
        } else {
            if (!is_string($slip_in['memo'])) {
                $success = false;
            } else {
                $slip_out['memo'] = $slip_in['memo'];
            }
        }

        return ['success' => $success, 'slip' => $slip_out];
    }
}
