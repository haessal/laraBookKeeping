<?php

namespace App\Http\Responder\api\v1;

use App\Http\Responder\api\BaseJsonResponder;
use Illuminate\Http\JsonResponse;

class BookJsonResponder extends BaseJsonResponder
{
    /**
     * Setup a new JSON response.
     *
     * @param  array{
     *   book: array{
     *     id: string,
     *     name: string,
     *     is_default: bool,
     *     is_owner: bool,
     *     modifiable: bool,
     *     owner: string,
     *   }
     * }  $context
     * @param  int  $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(array $context, int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        $this->response->setData($this->convert($context['book']));
        $this->response->setStatusCode($status);

        return $this->response;
    }

    /**
     * Convert the array to output JSON.
     *
     * @param  array{
     *   id: string,
     *   name: string,
     *   is_default: bool,
     *   is_owner: bool,
     *   modifiable: bool,
     *   owner: string,
     * }  $book
     * @return array{
     *   id: string,
     *   name: string,
     *   default: bool,
     *   own: bool,
     *   permitted_to: 'ReadWrite'|'ReadOnly',
     *   owner: string,
     * }
     */
    private function convert(array $book): array
    {
        $mode = ($book['modifiable'] == 1) ? 'ReadWrite' : 'ReadOnly';
        $bookItem = [
            'id'           => $book['id'],
            'name'         => $book['name'],
            'default'      => $book['is_default'],
            'own'          => $book['is_owner'],
            'permitted_to' => $mode,
            'owner'        => $book['owner'],
        ];

        return $bookItem;
    }
}
