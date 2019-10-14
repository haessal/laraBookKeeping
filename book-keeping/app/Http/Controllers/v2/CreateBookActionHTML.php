<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreateBookActionHTML extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function __invoke()
    {
        return view('home');
    }
}
