<?php

namespace App\Http\Responder\page\v2;

use Illuminate\Http\Response;

class ShowHomeViewResponder extends BaseViewResponder
{
    /**
     * Respond the ShowHomeView.
     *
     * @param  array  $context
     * @return Illuminate\Http\Response
     */
    public function response(array $context): Response
    {
        $this->response->setContent($this->view->make('bookkeeping.v2.pagehome', [
            'dropdownmenuLinks' => $this->dropdownMenuLinks(),
            'book'              => $context['book'],
            'selflinkname'      => 'v2_home',
            'navilinks'         => $this->navilinks(),
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
