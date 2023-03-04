<?php

namespace App\Http\Controllers\Settings;

use App\Http\Responder\Settings\UpdateAccessTokenViewResponder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UpdateAccessTokenActionHtml
{
    /**
     * UpdateAccessTokenView responder instance.
     *
     * @var \App\Http\Responder\Settings\UpdateAccessTokenViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Http\Responder\Settings\UpdateAccessTokenViewResponder  $responder
     * @return void
     */
    public function __construct(UpdateAccessTokenViewResponder $responder)
    {
        $this->responder = $responder;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request): Response
    {
        $context = [];
        $tokenText = null;
        $timestamp = null;

        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($request->isMethod('post')) {
            /** @var \Illuminate\Database\Eloquent\Builder $tokens */
            $tokens = $user->tokens();
            $tokens->delete();
            $tokenText = $user->createToken('personal-access-token')->plainTextToken;
        }
        if ($request->isMethod('delete')) {
            /** @var \Illuminate\Database\Eloquent\Builder $tokens */
            $tokens = $user->tokens();
            $tokens->delete();
        }
        foreach ($user->tokens as $token) {
            /** @var \Illuminate\Support\Carbon $created_at */
            $created_at = $token['created_at'];
            $timestamp = $created_at->format('Y-m-d H:i:s');
        }

        $context['token'] = $tokenText;
        $context['timestamp'] = $timestamp;

        return $this->responder->response($context);
    }
}
