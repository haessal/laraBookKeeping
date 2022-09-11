<?php

namespace App\Http\Responder\api\v1;

use App\Http\Responder\api\BaseJsonResponder;
use Illuminate\Http\JsonResponse;

class BookListJsonResponder extends BaseJsonResponder
{
    /**
     * Respond with the BookList JSON.
     *
     * @param  array  $context
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(array $context): JsonResponse
    {
        $this->response->setData($this->translateBookListFormat($context['books']));
        $this->response->setStatusCode(JsonResponse::HTTP_OK);

        return $this->response;
    }

    /**
     * Translate book list format for JSON.
     *
     * @param  array  $books
     * @return array
     */
    private function translateBookListFormat(array $books): array
    {
        $book_list = [];

        foreach ($books as $bookItem) {
            $isDefault = ($bookItem['is_default'] == 1) ? true : false;
            $own = ($bookItem['is_owner'] == 1) ? true : false;
            $mode = ($bookItem['modifiable'] == 1) ? 'ReadWrite' : 'ReadOnly';
            $book_list[] = [
                'id'           => $bookItem['id'],
                'name'         => $bookItem['name'],
                'default'      => $isDefault,
                'own'          => $own,
                'permitted_to' => $mode,
                'owner'        => $bookItem['owner'],
            ];
        }

        return $book_list;
    }
}
