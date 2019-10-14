<?php

namespace App\Http\Responder;

use Illuminate\Http\Response;
use Illuminate\Contracts\View\Factory as ViewFactory;

class ShowBookFormResponder
{
    protected $response;

    protected $view;

    public function __construct(Response $response, ViewFactory $view)
    {
        $this->response = $response;
        $this->view = $view;
    }

    /**
     * Response the Books list and Form to create new Book.
     * 
     * @return Illuminate\Http\Response
     */
    public function response(array $booklist): Response
    {
        $this->response->setStatusCode(Response::HTTP_OK);
        $this->response->setContent($this->view->make('book.list_and_createform'));
        return $this->response;
    }
}
