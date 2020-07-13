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
     * @param \App\Service\BookKeepingService                     $BookKeeping
     * @param \App\Http\Responder\api\v1\SlipEntriesJsonResponder $responder
     *
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
     * @param \Illuminate\Http\Request $request
     * @param string                   $slipEntryId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, string $slipEntryId): JsonResponse
    {
        $context = [];

        if (!($this->BookKeeping->validateUuid($slipEntryId))) {
            $response = new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        } else {
            $slips = $this->BookKeeping->retrieveSlipEntry($slipEntryId);
            if (empty($slips)) {
                $response = new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
            } else {
                $accounts = $this->BookKeeping->retrieveAccountsList();
                $result = $this->validateAndTrimSlipEntryContents($request->all(), $accounts);
                if (!$result['success']) {
                    $response = new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
                } else {
                    $this->BookKeeping->updateSlipEntry($slipEntryId, $result['slipEntryContents']);
                    $context['slips'] = $this->BookKeeping->retrieveSlipEntry($slipEntryId);
                    $response = $this->responder->response($context);
                }
            }
        }

        return $response;
    }

    /**
     * Validate slip entry contents and trim string data.
     *
     * @param array $slipEntryContents
     * @param array $accounts
     *
     * @return array
     */
    private function validateAndTrimSlipEntryContents(array $slipEntryContents, array $accounts): array
    {
        $success = true;
        $trimmed_slipEntryContents = [];

        foreach ($slipEntryContents as $contentsKey => $contentsItem) {
            switch ($contentsKey) {
                case 'debit':
                    $trimmed_slipEntryContents['debit'] = trim($contentsItem);
                    break;
                case 'credit':
                    $trimmed_slipEntryContents['credit'] = trim($contentsItem);
                    break;
                case 'amount':
                    $trimmed_slipEntryContents['amount'] = $contentsItem;
                    break;
                case 'client':
                    $trimmed_slipEntryContents['client'] = trim($contentsItem);
                    break;
                case 'outline':
                    $trimmed_slipEntryContents['outline'] = trim($contentsItem);
                    break;
                default:
                    $success = false;
                    break;
            }
        }
        if (empty($trimmed_slipEntryContents)) {
            $success = false;
        }
        if (array_key_exists('debit', $trimmed_slipEntryContents)) {
            if (empty($trimmed_slipEntryContents['debit']) || (!array_key_exists($trimmed_slipEntryContents['debit'], $accounts))) {
                $success = false;
            }
        }
        if (array_key_exists('credit', $trimmed_slipEntryContents)) {
            if (empty($trimmed_slipEntryContents['credit']) || (!array_key_exists($trimmed_slipEntryContents['credit'], $accounts))) {
                $success = false;
            }
        }
        if (array_key_exists('debit', $trimmed_slipEntryContents) && (!array_key_exists('credit', $trimmed_slipEntryContents))) {
            $success = false;
        }
        if ((!array_key_exists('debit', $trimmed_slipEntryContents)) && array_key_exists('credit', $trimmed_slipEntryContents)) {
            $success = false;
        }
        if (array_key_exists('debit', $trimmed_slipEntryContents) && array_key_exists('credit', $trimmed_slipEntryContents) && ($trimmed_slipEntryContents['debit'] == $trimmed_slipEntryContents['credit'])) {
            $success = false;
        }
        if (array_key_exists('amount', $trimmed_slipEntryContents)) {
            if (empty($trimmed_slipEntryContents['amount']) || (!is_int($trimmed_slipEntryContents['amount']))) {
                $success = false;
            }
        }
        if (array_key_exists('client', $trimmed_slipEntryContents)) {
            if (empty($trimmed_slipEntryContents['client'])) {
                $success = false;
            }
        }
        if (array_key_exists('outline', $trimmed_slipEntryContents)) {
            if (empty($trimmed_slipEntryContents['outline'])) {
                $success = false;
            }
        }
        if (!$success) {
            $trimmed_slipEntryContents = [];
        }

        return ['success' => $success, 'slipEntryContents' => $trimmed_slipEntryContents];
    }
}
