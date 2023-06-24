<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\api\AuthenticatedBookKeepingActionApi;
use App\Http\Responder\api\v1\BookAccessPermissionJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PutBooksPermissionsActionApi extends AuthenticatedBookKeepingActionApi
{
    /**
     * BookAccessPermissionJson responder instance.
     *
     * @var \App\Http\Responder\api\v1\BookAccessPermissionJsonResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Service\BookKeepingService  $BookKeeping
     * @param  \App\Http\Responder\api\v1\BookAccessPermissionJsonResponder  $responder
     * @return void
     */
    public function __construct(BookKeepingService $BookKeeping, BookAccessPermissionJsonResponder $responder)
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
        $result = $this->validateAndTrimPostBooksPermissionParameter($request->all());
        if ($result['success'] && $this->BookKeeping->validateUuid($bookId)) {
            $context = [];
            $mode = $result['mode'] == 'ReadWrite' ? 'ReadWrite' : 'ReadOnly';
            [$status, $permission] = $this->BookKeeping->authorizeToAccess($bookId, $result['user'], $mode);
            switch ($status) {
                case BookKeepingService::STATUS_NORMAL:
                    if (isset($permission)) {
                        $context['permission'] = $permission;
                        $response = $this->responder->response($context, JsonResponse::HTTP_CREATED);
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
     * @return array{success: bool, user: string, mode: 'ReadOnly'|'ReadWrite'|''}
     */
    private function validateAndTrimPostBooksPermissionParameter(array $parameter): array
    {
        $success = false;
        $trimmed_user = '';
        $trimmed_mode = '';
        if (array_key_exists('user', $parameter) && array_key_exists('mode', $parameter)) {
            if (is_string($parameter['user']) && is_string($parameter['mode'])) {
                $user = trim($parameter['user']);
                if (! empty($user)) {
                    $mode = trim($parameter['mode']);
                    if ((! empty($mode)) && (($mode == 'ReadOnly') || ($mode == 'ReadWrite'))) {
                        $success = true;
                        $trimmed_user = $user;
                        $trimmed_mode = $mode;
                    }
                }
            }
        }

        return ['success' => $success, 'user' => $trimmed_user,  'mode' => $trimmed_mode];
    }
}
