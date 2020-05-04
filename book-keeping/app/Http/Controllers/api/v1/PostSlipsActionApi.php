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
     * Validate and trim string data for accounts.
     *
     * @param array  $array_in
     * @param string $key
     * @param array  $accounts
     *
     * @return string|null
     */
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
     * @param array $slip_in
     * @param array $accounts
     *
     * @return array
     */
    private function validateAndTrimDraftSlip(array $slip_in, array $accounts): array
    {
        $success = true;
        $trimmed_slip = [];

        $trimmed_outline = $this->validateAndTrimString($slip_in, 'outline');
        if (is_null($trimmed_outline)) {
            $success = false;
        } else {
            $trimmed_slip['outline'] = $trimmed_outline;
        }
        $trimmed_date = $this->validateAndTrimString($slip_in, 'date');
        if (is_null($trimmed_date)) {
            $success = false;
        } else {
            if (!($this->BookKeeping->validateDateFormat($trimmed_date))) {
                $success = false;
            } else {
                $trimmed_slip['date'] = $trimmed_date;
            }
        }
        if (!array_key_exists('entries', $slip_in) || empty($slip_in['entries'])) {
            $success = false;
        } else {
            if (!is_array($slip_in['entries'])) {
                $success = false;
            } else {
                $trimmed_slip['entries'] = [];
                foreach ($slip_in['entries'] as $slipEntry_in) {
                    if (empty($slipEntry_in) || !is_array($slipEntry_in)) {
                        $success = false;
                    } else {
                        $result = $this->validateAndTrimDraftSlipEntry($slipEntry_in, $accounts);
                        $trimmed_slipEntry = $result['slipEntry'];
                        if (!empty($trimmed_slipEntry)) {
                            $trimmed_slip['entries'][] = $trimmed_slipEntry;
                        }
                        if (!$result['success']) {
                            $success = false;
                        }
                    }
                }
            }
        }
        if (!array_key_exists('memo', $slip_in) || is_null($slip_in['memo'])) {
            $trimmed_slip['memo'] = null;
        } else {
            if (!is_string($slip_in['memo'])) {
                $success = false;
            } else {
                $trimmed_memo = trim($slip_in['memo']);
                if (empty($trimmed_memo)) {
                    $trimmed_slip['memo'] = null;
                } else {
                    $trimmed_slip['memo'] = $trimmed_memo;
                }
            }
        }

        return ['success' => $success, 'slip' => $trimmed_slip];
    }

    /**
     * Validate draft slip entry and trim string data.
     *
     * @param array $slipEntry_in
     * @param array $accounts
     *
     * @return array
     */
    private function validateAndTrimDraftSlipEntry(array $slipEntry_in, array $accounts): array
    {
        $success = true;
        $trimmed_slipEntry = [];

        $trimmed_debit = $this->validateAndTrimAccounts($slipEntry_in, 'debit', $accounts);
        if (is_null($trimmed_debit)) {
            $success = false;
        } else {
            $trimmed_slipEntry['debit'] = $trimmed_debit;
        }
        $trimmed_credit = $this->validateAndTrimAccounts($slipEntry_in, 'credit', $accounts);
        if (is_null($trimmed_credit)) {
            $success = false;
        } else {
            $trimmed_slipEntry['credit'] = $trimmed_credit;
        }
        if (array_key_exists('debit', $trimmed_slipEntry) && array_key_exists('credit', $trimmed_slipEntry) && ($trimmed_slipEntry['debit'] == $trimmed_slipEntry['credit'])) {
            $success = false;
        }
        if (!array_key_exists('amount', $slipEntry_in) || empty($slipEntry_in['amount']) || !is_int($slipEntry_in['amount'])) {
            $success = false;
        } else {
            $trimmed_slipEntry['amount'] = $slipEntry_in['amount'];
        }
        $trimmed_client = $this->validateAndTrimString($slipEntry_in, 'client');
        if (is_null($trimmed_client)) {
            $success = false;
        } else {
            $trimmed_slipEntry['client'] = $trimmed_client;
        }
        $trimmed_outline = $this->validateAndTrimString($slipEntry_in, 'outline');
        if (is_null($trimmed_outline)) {
            $success = false;
        } else {
            $trimmed_slipEntry['outline'] = $trimmed_outline;
        }

        return ['success' => $success, 'slipEntry' => $trimmed_slipEntry];
    }

    /**
     * Validate and trim string data.
     *
     * @param array  $array_in
     * @param string $key
     *
     * @return string|null
     */
    private function validateAndTrimString(array $array_in, string $key): ?string
    {
        if (!array_key_exists($key, $array_in) || !is_string($array_in[$key])) {
            $string_out = null;
        } else {
            $trimmed_string = trim($array_in[$key]);
            if (empty($trimmed_string)) {
                $string_out = null;
            } else {
                $string_out = $trimmed_string;
            }
        }

        return $string_out;
    }
}
