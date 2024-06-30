<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Http\Responder\api\v1\CreditCardStatementJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetBooksCreditCardStatementsCreditCardStatementIdActionApi extends AuthenticatedBookKeepingActionApi
{
    /**
     * CreditCardStatementJson responder instance.
     *
     * @var \App\Http\Responder\api\v1\CreditCardStatementJsonResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\api\v1\CreditCardStatementJsonResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, CreditCardStatementJsonResponder $responder)
    {
        parent::__construct($BookKeeping);
        $this->responder = $responder;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $bookId
     * @param  string  $creditCardStatementId
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, string $bookId, string $creditCardStatementId): JsonResponse
    {
        $context = [];
        $response = null;

        if (! $this->BookKeeping->validateUuid($bookId)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }
        if (! $this->BookKeeping->validateUuid($creditCardStatementId)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        [$status, $creditCardStatement] = $this->BookKeeping->retrieveCreditCardStatement($creditCardStatementId, $bookId);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                if (isset($creditCardStatement)) {
                    $context['creditCardStatement'] = $creditCardStatement;
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
