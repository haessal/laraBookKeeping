<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Http\Responder\api\v1\SlipEntriesJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetSlipEntriesSlipEntryIdActionApi extends AuthenticatedBookKeepingActionApi
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

        if (! $this->BookKeeping->validateUuid($slipEntryId)) {
            $response = new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        } else {
            [$_, $slips] = $this->BookKeeping->retrieveSlipEntry($slipEntryId);
            if (empty($slips)) {
                $response = new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
            } else {
                $context['slips'] = $slips;
                $response = $this->responder->response($context);
            }
        }

        return $response;
    }
}
