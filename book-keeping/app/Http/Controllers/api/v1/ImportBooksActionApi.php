<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingMigrationActionApi;
use App\Http\Responder\api\v1\ImportBooksResultJsonResponder;
use App\Jobs\ProcessImportingBooks;
use App\Service\BookKeepingMigration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportBooksActionApi extends AuthenticatedBookKeepingMigrationActionApi
{
    /**
     * ImportedBooksJson responder instance.
     *
     * @var \App\Http\Responder\api\v1\ImportBooksResultJsonResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingMigration  $BookKeeping
     * @param  \App\Http\Responder\api\v1\ImportBooksResultJsonResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingMigration $BookKeeping, ImportBooksResultJsonResponder $responder)
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
        $response = null;

        $sourceUrl = $request->input('sourceUrl');
        if (! is_string($sourceUrl)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        $accessToken = $request->input('token');
        if (! is_string($accessToken)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        ProcessImportingBooks::dispatch(Auth::user(), $this->BookKeeping, $sourceUrl, $accessToken);

        return $this->responder->response($context, JsonResponse::HTTP_ACCEPTED);
    }
}
