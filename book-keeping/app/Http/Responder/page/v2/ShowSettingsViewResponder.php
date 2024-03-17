<?php

namespace App\Http\Responder\page\v2;

use Illuminate\Http\Response;

class ShowSettingsViewResponder extends BaseViewResponder
{
    /**
     * Respond the ShowSettingsView.
     *
     * @param  array{
     *   bookId: string,
     *   book: array{
     *     id: string,
     *     owner: string,
     *     name: string,
     *   },
     * }  $context
     * @return \Illuminate\Http\Response
     */
    public function response(array $context): Response
    {
        $this->response->setContent($this->view->make('bookkeeping.v2.pagesettings', [
            'bookId' => $context['bookId'],
            'book' => $context['book'],
            'selflinkname' => 'v2_settings',
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
