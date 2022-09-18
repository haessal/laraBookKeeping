<?php

namespace App\Http\Responder\api\v1;

use App\Http\Responder\api\BaseJsonResponder;
use Illuminate\Http\JsonResponse;

class BookJsonResponder extends BaseJsonResponder
{
    /**
     * Respond with the Book JSON.
     *
     * @param  array  $context
     * @param  int  $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(array $context, int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        $this->response->setData($this->translateBookFormat($context['book']));
        $this->response->setStatusCode($status);

        return $this->response;
    }

    /**
     * Translate book format for JSON.
     *
     * @param  array  $bookItem
     * @return array
     */
    private function translateBookFormat(array $bookItem): array
    {
        $isDefault = ($bookItem['is_default'] == 1) ? true : false;
        $own = ($bookItem['is_owner'] == 1) ? true : false;
        $mode = ($bookItem['modifiable'] == 1) ? 'ReadWrite' : 'ReadOnly';
        $book = [
            'id'           => $bookItem['id'],
            'name'         => $bookItem['name'],
            'default'      => $isDefault,
            'own'          => $own,
            'permitted_to' => $mode,
            'owner'        => $bookItem['owner'],
        ];

        return $book;
    }
}
