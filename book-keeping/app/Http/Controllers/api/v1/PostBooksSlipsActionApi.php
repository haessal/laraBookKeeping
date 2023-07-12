<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Http\Responder\api\v1\SlipJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostBooksSlipsActionApi extends AuthenticatedBookKeepingActionApi
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
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\api\v1\SlipJsonResponder  $responder
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, string $bookId): JsonResponse
    {
        $context = [];
        $slipEntries = [];
        $response = null;

        if (! $this->BookKeeping->validateUuid($bookId)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }
        $result = $this->validateAndTrimDraftSlip($request->all());
        if (! $result['success']) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        $slip = $result['slip'];
        foreach ($slip['entries'] as $index => $slipEntry) {
            $slipEntry['display_order'] = $index;
            $slipEntries[] = $slipEntry;
        }
        [$status, $slipId] = $this->BookKeeping->createSlip($slip['outline'], $slip['date'], $slipEntries, $slip['memo'], $bookId);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                if (isset($slipId)) {
                    [$retrievalStatus, $slips] = $this->BookKeeping->retrieveSlip($slipId, $bookId);
                    if ($retrievalStatus == BookKeepingService::STATUS_NORMAL) {
                        if (isset($slips)) {
                            $context['slip_id'] = strval($slipId);
                            $context['slip'] = $slips[$slipId];
                            $response = $this->responder->response($context, JsonResponse::HTTP_CREATED);
                        }
                    }
                }
                break;
            case BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE:
                $response = new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
                break;
            case BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN:
                $response = new JsonResponse(null, JsonResponse::HTTP_FORBIDDEN);
                break;
            case BookKeepingService::STATUS_ERROR_BAD_CONDITION:
                $response = new JsonResponse(null, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                break;
            default:
                break;
        }
        if (is_null($response)) {
            $response = new JsonResponse(null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * Validate 'debit' or 'credit' of the slip entry and trim string data.
     *
     * @param  array<string, mixed>  $slipEntry
     * @param  string  $key
     * @return string|null
     */
    private function validateAndTrimAccounts(array $slipEntry, string $key): ?string
    {
        $string_out = $this->validateAndTrimString($slipEntry, $key);
        if (isset($string_out)) {
            if (! $this->BookKeeping->validateUuid($string_out)) {
                $string_out = null;
            }
        }

        return $string_out;
    }

    /**
     * Validate the slip and trim string data.
     *
     * @param  array<string, mixed>  $slip
     * @return array{success: bool, slip: array{
     *   date: string,
     *   outline: string,
     *   memo: string|null,
     *   entries: array{
     *     debit: string,
     *     credit: string,
     *     amount: int,
     *     client: string,
     *     outline: string,
     *   }[],
     * }}
     */
    private function validateAndTrimDraftSlip(array $slip): array
    {
        $success = true;
        $trimmed_slip = [];

        $trimmed_outline = $this->validateAndTrimString($slip, 'outline');
        if (is_null($trimmed_outline)) {
            $success = false;
        } else {
            $trimmed_slip['outline'] = $trimmed_outline;
        }
        $trimmed_date = $this->validateAndTrimString($slip, 'date');
        if (is_null($trimmed_date)) {
            $success = false;
        } else {
            if (! $this->BookKeeping->validateDateFormat($trimmed_date)) {
                $success = false;
            } else {
                $trimmed_slip['date'] = $trimmed_date;
            }
        }
        if (! array_key_exists('entries', $slip) || empty($slip['entries'])) {
            $success = false;
        } else {
            if (! is_array($slip['entries'])) {
                $success = false;
            } else {
                $trimmed_slip['entries'] = [];
                foreach ($slip['entries'] as $slipEntry) {
                    if (empty($slipEntry) || ! is_array($slipEntry)) {
                        $success = false;
                    } else {
                        $result = $this->validateAndTrimDraftSlipEntry($slipEntry);
                        if ($result['success']) {
                            $trimmed_slip['entries'][] = $result['slipEntry'];
                        } else {
                            $success = false;
                        }
                    }
                }
            }
        }
        if (! array_key_exists('memo', $slip) || is_null($slip['memo'])) {
            $trimmed_slip['memo'] = null;
        } else {
            if (! is_string($slip['memo'])) {
                $success = false;
            } else {
                $trimmed_memo = trim($slip['memo']);
                if (empty($trimmed_memo)) {
                    $trimmed_slip['memo'] = null;
                } else {
                    $trimmed_slip['memo'] = $trimmed_memo;
                }
            }
        }

        return [
            'success' => $success,
            'slip'    => [
                'date'    => array_key_exists('date', $trimmed_slip) ? $trimmed_slip['date'] : '',
                'outline' => array_key_exists('outline', $trimmed_slip) ? $trimmed_slip['outline'] : '',
                'memo'    => array_key_exists('memo', $trimmed_slip) ? $trimmed_slip['memo'] : '',
                'entries' => array_key_exists('entries', $trimmed_slip) ? $trimmed_slip['entries'] : [],
            ],
        ];
    }

    /**
     * Validate the slip entry and trim string data.
     *
     * @param  array<string, mixed>  $slipEntry
     * @return array{success: bool, slipEntry: array{
     *   debit: string,
     *   credit: string,
     *   amount: int,
     *   client: string,
     *   outline: string,
     * }}
     */
    private function validateAndTrimDraftSlipEntry(array $slipEntry): array
    {
        $success = true;
        $trimmed_slipEntry = [];

        $trimmed_debit = $this->validateAndTrimAccounts($slipEntry, 'debit');
        if (is_null($trimmed_debit)) {
            $success = false;
        } else {
            $trimmed_slipEntry['debit'] = $trimmed_debit;
        }
        $trimmed_credit = $this->validateAndTrimAccounts($slipEntry, 'credit');
        if (is_null($trimmed_credit)) {
            $success = false;
        } else {
            $trimmed_slipEntry['credit'] = $trimmed_credit;
        }
        if (array_key_exists('debit', $trimmed_slipEntry) && array_key_exists('credit', $trimmed_slipEntry) && ($trimmed_slipEntry['debit'] == $trimmed_slipEntry['credit'])) {
            $success = false;
        }
        if (! array_key_exists('amount', $slipEntry) || empty($slipEntry['amount']) || ! is_int($slipEntry['amount'])) {
            $success = false;
        } else {
            $trimmed_slipEntry['amount'] = $slipEntry['amount'];
        }
        $trimmed_client = $this->validateAndTrimString($slipEntry, 'client');
        if (is_null($trimmed_client)) {
            $success = false;
        } else {
            $trimmed_slipEntry['client'] = $trimmed_client;
        }
        $trimmed_outline = $this->validateAndTrimString($slipEntry, 'outline');
        if (is_null($trimmed_outline)) {
            $success = false;
        } else {
            $trimmed_slipEntry['outline'] = $trimmed_outline;
        }

        return [
            'success'   => $success,
            'slipEntry' => [
                'debit'   => array_key_exists('debit', $trimmed_slipEntry) ? strval($trimmed_slipEntry['debit']) : '',
                'credit'  => array_key_exists('credit', $trimmed_slipEntry) ? strval($trimmed_slipEntry['credit']) : '',
                'amount'  => array_key_exists('amount', $trimmed_slipEntry) ? intval($trimmed_slipEntry['amount']) : 0,
                'client'  => array_key_exists('client', $trimmed_slipEntry) ? strval($trimmed_slipEntry['client']) : '',
                'outline' => array_key_exists('outline', $trimmed_slipEntry) ? strval($trimmed_slipEntry['outline']) : '',
            ],
        ];
    }

    /**
     * Validate and trim string data.
     *
     * @param  array<string, mixed>  $array_in
     * @param  string  $key
     * @return string|null
     */
    private function validateAndTrimString(array $array_in, string $key): ?string
    {
        if (! array_key_exists($key, $array_in) || ! is_string($array_in[$key])) {
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
