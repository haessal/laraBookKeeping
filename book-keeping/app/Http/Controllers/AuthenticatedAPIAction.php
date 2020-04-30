<?php

namespace App\Http\Controllers;

class AuthenticatedAPIAction extends Controller
{
    /**
     * Create a new AuthenticatedAPIAction instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
}
