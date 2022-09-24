<?php

namespace App\Http\Responder\api\v1;

use App\Http\Responder\api\BaseJsonResponder;
use Illuminate\Http\JsonResponse;

class BookAccessPermissionListJsonResponder extends BaseJsonResponder
{
    /**
     * Respond with the BookAccessPermissionList JSON.
     *
     * @param  array  $context
     * @param  int  $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(array $context, int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        $this->response->setData(($context['permission_list']));
        $this->response->setStatusCode($status);

        return $this->response;
    }
}
