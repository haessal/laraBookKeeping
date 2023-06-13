<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteBooksPermissionsActionApi extends AuthenticatedBookKeepingActionApi
{
    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping)
    {
        parent::__construct($BookKeeping);
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
        $result = $this->validateAndTrimDeleteBooksPermissionParameter($request->all());
        if ($result['success'] && $this->BookKeeping->validateUuid($bookId)) {
            [$status, $permission] = $this->BookKeeping->forbidToAccess($bookId, $result['user']);
            switch ($status) {
                case BookKeepingService::STATUS_NORMAL:
                    if (isset($permission)) {
                        $response = new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
                    } else {
                        $response = new JsonResponse(null, JsonResponse::HTTP_OK);
                    }
                    break;
                case BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE:
                    $response = new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
                    break;
                case BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN:
                    $response = new JsonResponse(null, JsonResponse::HTTP_FORBIDDEN);
                    break;
                default:
                    $response = new JsonResponse(null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
                    break;
            }
        } else {
            $response = new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
        }

        return $response;
    }

    /**
     * Validate the parameter and trim string data.
     *
     * @param  array<string, mixed>  $parameter
     * @return array{success: bool, user: string}
     */
    private function validateAndTrimDeleteBooksPermissionParameter(array $parameter): array
    {
        $success = false;
        $trimmed_user = '';
        if (array_key_exists('user', $parameter) && is_string($parameter['user'])) {
            $user = trim($parameter['user']);
            if (! empty($user)) {
                $success = true;
                $trimmed_user = $user;
            }
        }

        return ['success' => $success, 'user' => $trimmed_user];
    }
}