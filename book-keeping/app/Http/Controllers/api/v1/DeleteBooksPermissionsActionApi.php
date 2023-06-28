<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Http\Responder\api\v1\BookAccessPermissionListJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteBooksPermissionsActionApi extends AuthenticatedBookKeepingActionApi
{
    /**
     * BookAccessPermissionListJson responder instance.
     *
     * @var \App\Http\Responder\api\v1\BookAccessPermissionListJsonResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\api\v1\BookAccessPermissionListJsonResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, BookAccessPermissionListJsonResponder $responder)
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
            $result = $this->validateAndTrimDeleteBooksPermissionParameter($request->all());
            if (! $result['success']) {
                return new JsonResponse(null, JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        [$status, $_] = $this->BookKeeping->forbidToAccess($bookId, $result['user']);
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                [$retrievalStatus, $permissionList] = $this->BookKeeping->retrievePermittedUsers($bookId);
                if ($retrievalStatus == BookKeepingService::STATUS_NORMAL) {
                    $context['permission_list'] = $permissionList;
                    $response = $this->responder->response($context);
                }
                break;
            case BookKeepingService::STATUS_ERROR_AUTH_NOTAVAILABLE:
                $response = new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
                break;
            case BookKeepingService::STATUS_ERROR_AUTH_FORBIDDEN:
                $response = new JsonResponse(null, JsonResponse::HTTP_FORBIDDEN);
                break;
            case BookKeepingService::STATUS_ERROR_BAD_CONDITION:
                $response = new JsonResponse(null, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
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
