<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Http\Responder\api\v1\BookListJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetBooksActionApi extends AuthenticatedBookKeepingActionApi
{
    /**
     * BookListJson responder instance.
     *
     * @var \App\Http\Responder\api\v1\BookListJsonResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\api\v1\BookListJsonResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, BookListJsonResponder $responder)
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
        $context['books'] = $this->BookKeeping->retrieveAvailableBook();

        return $this->responder->response($context);
    }
}
