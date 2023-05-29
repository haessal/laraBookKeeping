<?php

namespace App\Http\Responder\api\v1;

use App\Http\Responder\api\BaseJsonResponder;
use Illuminate\Http\JsonResponse;

class BookAccessPermissionJsonResponder extends BaseJsonResponder
{
    /**
     * Setup a new JSON response.
     *
     * @param  array{
     *   permission: array{
     *     user: string,
     *     permitted_to: 'ReadWrite'|'ReadOnly'
     *   }
     * }  $context
     * @param  int  $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(array $context, int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        $this->response->setData($context['permission']);
        $this->response->setStatusCode($status);

        return $this->response;
    }
}
