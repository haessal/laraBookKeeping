<?php

namespace App\Http\Controllers;

class AuthenticatedApiAction extends Controller
{
    /**
     * Create a new AuthenticatedApiAction instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
}
