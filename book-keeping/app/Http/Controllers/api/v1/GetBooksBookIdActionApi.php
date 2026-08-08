<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Http\Responder\api\v1\BookJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetBooksBookIdActionApi extends AuthenticatedBookKeepingActionApi
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
     * @param  string  $bookId
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, string $bookId): JsonResponse
    {
        $context = [];

        if (! $this->BookKeeping->validateUuid($bookId)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        $book = $this->BookKeeping->retrieveBook($bookId);
        if (isset($book)) {
            $context['book'] = $book;
            $response = $this->responder->response($context);
        } else {
            $response = new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
        }

        return $response;
    }
}
