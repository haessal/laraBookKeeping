<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\AuthenticatedAction;
use App\Http\Responder\Settings\UpdateAccessTokenViewResponder;
use App\Service\AccessTokenService;
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

        if ($request->isMethod('post')) {
            $request->user()->tokens()->delete();
            $tokenText = $request->user()->createToken('personal-access-token')->plainTextToken;
        }
        if ($request->isMethod('delete')) {
            $request->user()->tokens()->delete();
        }
        foreach ($request->user()->tokens as $token) {
            $timestamp = $token['created_at']->format('Y-m-d H:i:s');
        }

        $context['token'] = $tokenText;
        $context['timestamp'] = $timestamp;

        return $this->responder->response($context);
    }
}
