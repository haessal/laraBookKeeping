<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteSlipEntriesActionApi extends AuthenticatedBookKeepingActionApi
{
    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping)
    {
        parent::__construct($BookKeeping);
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
        $response = null;

        if (! $this->BookKeeping->validateUuid($slipEntryId)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        [$status, $_] = $this->BookKeeping->deleteSlipEntryAndEmptySlip($slipEntryId);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                $response = new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
                break;
            case BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE:
                $response = new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
                break;
            default:
                break;
        }
        if (is_null($response)) {
            $response = new JsonResponse(null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }
}
