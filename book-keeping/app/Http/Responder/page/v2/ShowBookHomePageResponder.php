<?php

namespace App\Http\Responder\page\v2;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class ShowBookHomePageResponder
{
    /**
     * Respond the ShowBookHomePage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array{
     *   book: array{
     *     id: string,
     *     name: string,
     *     is_default: bool,
     *     is_owner: bool,
     *     modifiable: bool,
     *     owner: string,
     *   },
     * }  $context
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(Request $request, array $context): Response
    {
        $book = $context['book'];

        return Inertia::render('BookKeeping/v2/Home', [
            'book' => $book,
        ])->toResponse($request);
    }
}
