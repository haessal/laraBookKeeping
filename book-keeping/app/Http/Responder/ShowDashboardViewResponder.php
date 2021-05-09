<?php

namespace App\Http\Responder;

use Illuminate\Http\Response;

class ShowDashboardViewResponder extends BaseLayoutViewResponder
{
    /**
     * Respond the ShowDashboardView.
     *
     * @param array $context
     *
     * @return Illuminate\Http\Response
     */
    public function response(array $context): Response
    {
        $this->response->setContent($this->view->make('home', [
            'dropdownmenuLinks' => $this->dropdownMenuLinks(),
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
