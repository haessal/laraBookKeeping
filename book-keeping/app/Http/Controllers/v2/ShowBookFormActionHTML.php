<?php

namespace App\Http\Controllers;

use App\Http\Responder\ShowBookFormResponder;
use App\Service\BookService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShowBookFormActionHTML extends Controller
{
    private $service;
    
    private $responder;

    public function __construct(BookService $service, ShowBookFormResponder $responder)
    {
        $this->middleware('auth');
        $this->middleware('verified');
        $this->service = $service;
        $this->responder = $responder;
    }

    /**
     * Show the Books list and Form to create new Book.
     * 
     * @param  Illuminate\Http\Request
     *
     * @return Illuminate\Http\Response
     */
    public function __invoke(Request $request): Response
    {
        return $this->responder->response($this->service->retrieveBookList($request->user()->id));
    }
}
