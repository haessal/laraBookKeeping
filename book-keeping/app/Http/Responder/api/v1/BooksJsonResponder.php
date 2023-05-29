<?php

namespace App\Http\Responder\api\v1;

use App\Http\Responder\api\BaseJsonResponder;
use Illuminate\Http\JsonResponse;

class BooksJsonResponder extends BaseJsonResponder
{
    /**
     * Setup a new JSON response.
     *
     * @param  array{
     *   books: array{
     *     id: string,
     *     name: string,
     *     is_default: bool,
     *     is_owner: bool,
     *     modifiable: bool,
     *     owner: string,
     *   }[]
     * }  $context
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(array $context): JsonResponse
    {
        $this->response->setData($this->convert($context['books']));
        $this->response->setStatusCode(JsonResponse::HTTP_OK);

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
     * }[]  $books
     * @return array{
     *   id: string,
     *   name: string,
     *   default: bool,
     *   own: bool,
     *   permitted_to: 'ReadWrite'|'ReadOnly',
     *   owner: string,
     * }[]
     */
    private function convert(array $books): array
    {
        $bookList = [];

        foreach ($books as $bookItem) {
            $mode = ($bookItem['modifiable'] == 1) ? 'ReadWrite' : 'ReadOnly';
            $bookList[] = [
                'id'           => $bookItem['id'],
                'name'         => $bookItem['name'],
                'default'      => $bookItem['is_default'],
                'own'          => $bookItem['is_owner'],
                'permitted_to' => $mode,
                'owner'        => $bookItem['owner'],
            ];
        }

        return $bookList;
    }
}
