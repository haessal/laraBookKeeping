<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Http\Responder\api\v1\SlipJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetSlipsSlipIdActionApi extends AuthenticatedBookKeepingActionApi
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
     * @param string                   $slipId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, string $slipId): JsonResponse
    {
        $context = [];

        if (!($this->BookKeeping->validateUuid($slipId))) {
            $response = new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        } else {
            $slips = $this->BookKeeping->retrieveSlip($slipId);
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
