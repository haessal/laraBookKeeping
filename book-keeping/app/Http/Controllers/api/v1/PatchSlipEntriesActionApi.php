<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Http\Responder\api\v1\SlipEntriesJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatchSlipEntriesActionApi extends AuthenticatedBookKeepingActionApi
{
    /**
     * SlipEntriesJson responder instance.
     *
     * @var \App\Http\Responder\api\v1\SlipEntriesJsonResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\api\v1\SlipEntriesJsonResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, SlipEntriesJsonResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slipEntryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, string $slipEntryId): JsonResponse
    {
        $context = [];
        $response = null;

        if (! $this->BookKeeping->validateUuid($slipEntryId)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }
        $result = $this->validateAndTrimSlipEntryContents($request->all());
        if (! $result['success']) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        $slipEntryContents = [];
        if (array_key_exists('debit', $request->all())) {
            $slipEntryContents['debit'] = $result['slipEntryContents']['debit'];
        }
        if (array_key_exists('credit', $request->all())) {
            $slipEntryContents['credit'] = $result['slipEntryContents']['credit'];
        }
        if (array_key_exists('amount', $request->all())) {
            $slipEntryContents['amount'] = $result['slipEntryContents']['amount'];
        }
        if (array_key_exists('client', $request->all())) {
            $slipEntryContents['client'] = $result['slipEntryContents']['client'];
        }
        if (array_key_exists('outline', $request->all())) {
            $slipEntryContents['outline'] = $result['slipEntryContents']['outline'];
        }
        [$status, $_] = $this->BookKeeping->updateSlipEntry($slipEntryId, $slipEntryContents);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                [$retrievalStatus, $slips] = $this->BookKeeping->retrieveSlipEntry($slipEntryId);
                if ($retrievalStatus == BookKeepingService::STATUS_NORMAL) {
                    if (isset($slips)) {
                        $context['slips'] = $slips;
                        $response = $this->responder->response($context);
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
     * Validate the slip entry contents and trim string data.
     *
     * @param  array<string, mixed>  $slipEntryContents
     * @return array{success: bool, slipEntryContents: array{
     *   debit: string,
     *   credit: string,
     *   amount: int,
     *   client: string,
     *   outline: string,
     * }}
     */
    private function validateAndTrimSlipEntryContents(array $slipEntryContents): array
    {
        $success = true;
        $trimmed = [];

        foreach ($slipEntryContents as $contentsKey => $contentsItem) {
            switch ($contentsKey) {
                case 'debit':
                    $trimmed['debit'] = trim(strval($contentsItem));
                    break;
                case 'credit':
                    $trimmed['credit'] = trim(strval($contentsItem));
                    break;
                case 'amount':
                    $trimmed['amount'] = intval($contentsItem);
                    break;
                case 'client':
                    $trimmed['client'] = trim(strval($contentsItem));
                    break;
                case 'outline':
                    $trimmed['outline'] = trim(strval($contentsItem));
                    break;
                default:
                    $success = false;
                    break;
            }
        }
        if (empty($trimmed)) {
            $success = false;
        }
        if (array_key_exists('debit', $trimmed)) {
            if (! $this->BookKeeping->validateUuid(strval($trimmed['debit']))) {
                $success = false;
            }
        }
        if (array_key_exists('credit', $trimmed)) {
            if (! $this->BookKeeping->validateUuid(strval($trimmed['credit']))) {
                $success = false;
            }
        }
        if (array_key_exists('debit', $trimmed) && (! array_key_exists('credit', $trimmed))) {
            $success = false;
        }
        if ((! array_key_exists('debit', $trimmed)) && array_key_exists('credit', $trimmed)) {
            $success = false;
        }
        if (array_key_exists('debit', $trimmed) && array_key_exists('credit', $trimmed) && ($trimmed['debit'] == $trimmed['credit'])) {
            $success = false;
        }
        if (array_key_exists('amount', $trimmed)) {
            if (empty($trimmed['amount']) || (! is_int($trimmed['amount']))) {
                $success = false;
            }
        }
        if (array_key_exists('client', $trimmed)) {
            if (empty($trimmed['client'])) {
                $success = false;
            }
        }
        if (array_key_exists('outline', $trimmed)) {
            if (empty($trimmed['outline'])) {
                $success = false;
            }
        }

        return [
            'success'           => $success,
            'slipEntryContents' => [
                'debit'   => array_key_exists('debit', $trimmed) ? strval($trimmed['debit']) : '',
                'credit'  => array_key_exists('credit', $trimmed) ? strval($trimmed['credit']) : '',
                'amount'  => array_key_exists('amount', $trimmed) ? intval($trimmed['amount']) : 0,
                'client'  => array_key_exists('client', $trimmed) ? strval($trimmed['client']) : '',
                'outline' => array_key_exists('outline', $trimmed) ? strval($trimmed['outline']) : '',
            ],
        ];
    }
}
