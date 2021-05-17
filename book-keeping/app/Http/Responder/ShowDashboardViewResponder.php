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
        $book_list = empty($context['books']) ? null : $context['books'];

        $this->response->setContent($this->view->make('home', [
            'dropdownmenuLinks' => $this->dropdownMenuLinks(),
            'book_list'         => $book_list,
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}
