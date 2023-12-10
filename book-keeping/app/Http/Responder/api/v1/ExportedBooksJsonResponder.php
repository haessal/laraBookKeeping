<?php

namespace App\Http\Responder\api\v1;

use App\Http\Responder\api\BaseJsonResponder;
use Illuminate\Http\JsonResponse;

class ExportedBooksJsonResponder extends BaseJsonResponder
{
    /**
     * Respond with the ExportedBooks JSON.
     *
     * @param  array{
     *   version: string,
     *   books: array<string, array{
     *     book?: array{
     *       book_id?: string,
     *       book_name?: string,
     *       display_order?: int|null,
     *       created_at?: string|null,
     *       updated_at?: string|null,
     *       deleted_at?: string|null,
     *     }|null,
     *     accounts?: array<string, array{
     *       account_group_id?: string,
     *       book_id?: string,
     *       account_type?: string,
     *       account_group_title?: string,
     *       bk_uid?: int|null,
     *       account_group_bk_code?: int|null,
     *       is_current?: bool,
     *       display_order?: int|null,
     *       created_at?: string|null,
     *       updated_at?: string|null,
     *       deleted_at?: string|null,
     *       items?: array<string, array{
     *         account_id?: string,
     *         account_group_id?: string,
     *         account_title?: string,
     *         description?: string,
     *         selectable?: bool,
     *         bk_uid?: int|null,
     *         account_bk_code?: int|null,
     *         display_order?: int|null,
     *         created_at?: string|null,
     *         updated_at?: string|null,
     *         deleted_at?: string|null,
     *       }>,
     *     }>,
     *     slips?: array<string, array{
     *       slip_id?: string,
     *       book_id?: string,
     *       slip_outline?: string,
     *       slip_memo?: string|null,
     *       date?: string,
     *       is_draft?: bool,
     *       display_order?: int|null,
     *       created_at?: string|null,
     *       updated_at?: string|null,
     *       deleted_at?: string|null,
     *       entries?: array<string, array{
     *         slip_entry_id?: string,
     *         slip_id?: string,
     *         debit?: string,
     *         credit?: string,
     *         amount?: int,
     *         client?: string,
     *         outline?: string,
     *         display_order?: int|null,
     *         created_at?: string|null,
     *         updated_at?: string|null,
     *         deleted_at?: string|null,
     *       }>,
     *     }>,
     *   }>,
     * }  $context
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(array $context): JsonResponse
    {
        $this->response->setData($context);
        $this->response->setStatusCode(JsonResponse::HTTP_OK);

        return $this->response;
    }
}
