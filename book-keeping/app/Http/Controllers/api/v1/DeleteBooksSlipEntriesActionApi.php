<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteBooksSlipEntriesActionApi extends AuthenticatedBookKeepingActionApi
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
    public function __invoke(Request $request, string $bookId, string $slipEntryId): JsonResponse
    {
        if (! $this->BookKeeping->validateUuid($slipEntryId)) {
            $response = new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        } else {
            $slips = $this->BookKeeping->retrieveSlipEntry($slipEntryId);
            if (empty($slips)) {
                $response = new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
            } else {
                $this->BookKeeping->deleteSlipEntryAndEmptySlip($slipEntryId);
                $response = new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
            }
        }

        return $response;
    }
}
