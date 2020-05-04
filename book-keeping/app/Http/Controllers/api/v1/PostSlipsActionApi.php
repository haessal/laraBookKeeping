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


    private function validateAndTrimString(array $array_in, string $key): ?string
    {
        if (!array_key_exists($key, $array_in) || !is_string($array_in[$key])) {
            $string_out = null;
        } else {
            $trim_string = trim($array_in[$key]);
            if (empty($trim_string)) {
                $string_out = null;
            } else {
                $string_out = $trim_string;
            }
        }

        return $string_out;
    }

    private function validateAndTrimAccounts(array $array_in, string $key, array $accounts): ?string
    {
        $string_out = $this->validateAndTrimString($array_in, $key);
        if (!is_null($string_out)) {
            if (!array_key_exists($string_out, $accounts)) {
                $string_out = null;
            }
        }

        return $string_out;
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

        $trim_outline = $this->validateAndTrimString($slip_in, 'outline');
        if (is_null($trim_outline)) {
            $success = false;
        } else {
            $slip_out['outline'] = $trim_outline;
        }
        $trim_date = $this->validateAndTrimString($slip_in, 'date');
        if (is_null($trim_date)) {
            $success = false;
        } else {
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
                        $trim_debit = $this->validateAndTrimString($slipEntry_in, 'debit');
                        if (is_null($trim_debit)) {
                            $success = false;
                        } else {
                            if (!array_key_exists($trim_debit, $accounts)) {
                                $success = false;
                            } else {
                                $slipEntry_out['debit'] = $trim_debit;
                            }
                        }
                        $trim_credit = $this->validateAndTrimString($slipEntry_in, 'credit');
                        if (is_null($trim_credit)) {
                            $success = false;
                        } else {
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
                        $trim_client = $this->validateAndTrimString($slipEntry_in, 'client');
                        if (is_null($trim_client)) {
                            $success = false;
                        } else {
                            $slipEntry_out['client'] = $trim_client;
                        }
                        $trim_entry_outline = $this->validateAndTrimString($slipEntry_in, 'outline');
                        if (is_null($trim_entry_outline)) {
                            $success = false;
                        } else {
                            $slipEntry_out['outline'] = $trim_entry_outline;
                        }
                        if (!empty($slipEntry_out)) {
                            $slip_out['entries'][] = $slipEntry_out;
                        }
                    }
                }
            }
        }
        if (!array_key_exists('memo', $slip_in) || is_null($slip_in['memo'])) {
            $slip_out['memo'] = null;
        } else {
            if (!is_string($slip_in['memo'])) {
                $success = false;
            } else {
                $trim_memo = trim($slip_in['memo']);
                if (empty($trim_memo)) {
                    $slip_out['memo'] = null;
                } else {
                    $slip_out['memo'] = $trim_memo;
                }
            }
        }

        return ['success' => $success, 'slip' => $slip_out];
    }
}
