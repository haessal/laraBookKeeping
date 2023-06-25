<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Http\Responder\api\v1\BookJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatchBooksActionApi extends AuthenticatedBookKeepingActionApi
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
        $response = null;

        if (! $this->BookKeeping->validateUuid($bookId)) {
            return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        } else {
            $result = $this->validateAndTrimPatchBooksParameter($request->all());
            if (! $result['success']) {
                return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        [$status, $_] = $this->BookKeeping->updateBookName($bookId, $result['name']);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                $book = $this->BookKeeping->retrieveBook($bookId);
                if (isset($book)) {
                    $context['book'] = $book;
                    $response = $this->responder->response($context);
                }
                break;
            case BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE:
                $response = new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
                break;
            case BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN:
                $response = new JsonResponse(null, JsonResponse::HTTP_FORBIDDEN);
                break;
            default:
                break;
        }
        if (is_null($response)) {
            $response = new JsonResponse(null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    /**
     * Validate the parameter and trim string data.
     *
     * @param  array<string, mixed>  $parameter
     * @return array{success: bool, name: string}
     */
    private function validateAndTrimPatchBooksParameter(array $parameter): array
    {
        $success = false;
        $trimmed_name = '';
        if (array_key_exists('name', $parameter) && is_string($parameter['name'])) {
            $name = trim($parameter['name']);
            if (! empty($name)) {
                $success = true;
                $trimmed_name = $name;
            }
        }

        return ['success' => $success, 'name' => $trimmed_name];
    }
}
