<?php

namespace App\Http\Responder\api\v1;

use App\Http\Responder\api\BaseJsonResponder;
use Illuminate\Http\JsonResponse;

class BookAccessPermissionListJsonResponder extends BaseJsonResponder
{
    /**
     * Setup a new JSON response.
     *
     * @param  array{
     *   permission_list: array{
     *     user: string,
     *     permitted_to: 'ReadWrite'|'ReadOnly'
     *   }[]
     * }  $context
     * @param  int  $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(array $context, int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        $this->response->setData($context['permission_list']);
        $this->response->setStatusCode($status);

        return $this->response;
    }
}
