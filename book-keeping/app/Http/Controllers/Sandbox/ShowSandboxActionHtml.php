<?php

namespace App\Http\Controllers\Sandbox;

use Illuminate\Http\Request;

class ShowSandboxActionHtml
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return view('sandbox.show');
    }
}
