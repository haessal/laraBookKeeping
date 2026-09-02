<?php

namespace App\Http\Responder\page;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class ShowDashboardPageResponder
{
    /**
     * Respond the ShowDashboardPage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array{
     *   books: array{
     *     id: string,
     *     name: string,
     *     is_default: bool,
     *     is_owner: bool,
     *     modifiable: bool,
     *     owner: string,
     *   }[],
     * }  $context
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(Request $request, array $context): Response
    {
        $book_list = empty($context['books']) ? null : $context['books'];

        return Inertia::render('Dashboard', [
            'book_list' => $book_list,
        ])->toResponse($request);
    }
}
