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
        $response = null;

        if (! $this->BookKeeping->validateUuid($slipEntryId)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        [$status, $slips] = $this->BookKeeping->retrieveSlipEntry($slipEntryId);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                if (isset($slips)) {
                    $context['slips'] = $slips;
                    $response = $this->responder->response($context);
                }
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
