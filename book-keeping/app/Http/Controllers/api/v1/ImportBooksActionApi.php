<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Http\Responder\api\v1\ImportBooksResultJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImportBooksActionApi extends AuthenticatedBookKeepingActionApi
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
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\api\v1\ImportBooksResultJsonResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, ImportBooksResultJsonResponder $responder)
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

        [$status, $importResult] = $this->BookKeeping->importBooks($sourceUrl, $accessToken);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                if (isset($importResult)) {
                    $context['sourceUrl'] = $sourceUrl;
                    $context['result'] = $importResult;
                    $response = $this->responder->response($context);
                }
                break;
            default:
                // return new JsonResponse(null, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                break;
        }
        if (is_null($response)) {
            $response = new JsonResponse(null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }
}
