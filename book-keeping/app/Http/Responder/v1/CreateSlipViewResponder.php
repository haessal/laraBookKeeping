<?php

namespace App\Http\Responder\v1;

use Illuminate\Http\Response;

class CreateSlipViewResponder extends BaseViewResponder
{
    /**
     * Response the Form to create new Slip.
     *
     * @param array $context
     *
     * @return Illuminate\Http\Response
     */
    public function response(array $context): Response
    {
        $this->response->setContent($this->view->make('bookkeeping.v1.pageslip', [
            'navilinks'        => $this->navilinks(),
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
