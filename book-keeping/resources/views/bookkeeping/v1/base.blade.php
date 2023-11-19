<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>@yield('pagetitle') - {{ config('app.name', 'Laravel') }}</title>

        <!-- Styles -->
        <link href="{{ asset('css/v1_base.css') . '?ver=1.0.0' }}" rel="stylesheet" type="text/css" />
    </head>
    <body>
        @guest {{ __('You need login') }} @else
        <div id="logout">
            <b>{{ __('login as') }} {{ Auth::user()->name }}</b>
            [
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
            ]
        </div>
        <div id="titlelogo">
            <h1>{{ __('phpBookKeeping') }}</h1>
        </div>
        <div id="navigation-bar">
            @foreach ($navigation as $navigationItem) [
            <a href="{{ route( $navigationItem['link'] ) }}">{{{ $navigationItem['caption'] }}}</a>
            ]&nbsp @endforeach
        </div>
        <hr />
        @yield('content') @endguest
        <address>(C) 2007 haessal&nbsp</address>
    </body>
</html>
