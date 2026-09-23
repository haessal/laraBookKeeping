<?php

namespace App\Http\Responder\page;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CreateBookPageResponder
{
    /**
     * Respond the CreateBookPage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(Request $request): Response
    {
        return Inertia::render('BookCreate')->toResponse($request);
    }
}
