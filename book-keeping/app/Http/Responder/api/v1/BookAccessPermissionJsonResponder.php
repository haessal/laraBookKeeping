<?php

namespace App\Http\Responder\api\v1;

use App\Http\Responder\api\BaseJsonResponder;
use Illuminate\Http\JsonResponse;

class BookAccessPermissionJsonResponder extends BaseJsonResponder
{
    /**
     * Respond with the BookAccessPermission JSON.
     *
     * @param  array  $context
     * @param  int  $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(array $context, int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        $this->response->setData(($context['permission']));
        $this->response->setStatusCode($status);

        return $this->response;
    }
}
