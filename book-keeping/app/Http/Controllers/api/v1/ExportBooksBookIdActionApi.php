<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingMigrationActionApi;
use App\Http\Responder\api\v1\ExportedBooksJsonResponder;
use App\Service\BookKeepingMigration;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExportBooksBookIdActionApi extends AuthenticatedBookKeepingMigrationActionApi
{
    /**
     * ExportedBooksJsonResponder responder instance.
     *
     * @var \App\Http\Responder\api\v1\ExportedBooksJsonResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingMigration  $BookKeeping
     * @param  \App\Http\Responder\api\v1\ExportedBooksJsonResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingMigration $BookKeeping, ExportedBooksJsonResponder $responder)
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
    public function __invoke(Request $request, string $bookId): JsonResponse
    {
        $context = [];
        $response = null;

        if (! $this->BookKeeping->validateUuid($bookId)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        [$status, $books] = $this->BookKeeping->exportBook($bookId);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                if (isset($books)) {
                    $context['version'] = '2.0';
                    $context['books'] = $books;
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
