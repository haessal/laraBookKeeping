<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Http\Responder\api\v1\BookJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetBooksDefaultActionApi extends AuthenticatedBookKeepingActionApi
{
    /**
     * BookJson responder instance.
     *
     * @var \App\Http\Responder\api\v1\BookJsonResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\api\v1\BookJsonResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, BookJsonResponder $responder)
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
    public function __invoke(Request $request): JsonResponse
    {
        $context = [];
        [$status, $book] = $this->BookKeeping->retrieveDefaultBook();
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                $context['book'] = $book;
                $response = $this->responder->response($context);
                break;
            case BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE:
                $response = new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
                break;
            default:
                break;
        }

        return $response;
    }
}
