<?php

namespace App\Http\Controllers;

class AuthenticatedActionApi extends Controller
{
    /**
     * Create a new AuthenticatedActionApi instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
}
