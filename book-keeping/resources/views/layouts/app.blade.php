<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        @if (isset($title))
        <title>{{ $title }} - {{ config('app.name', 'BookKeeping') }}</title>
        @else
        <title>{{ config('app.name', 'BookKeeping') }}</title>
        @endif

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="bg-white">
        <div class="min-h-screen">
            <!-- Page Heading -->
            <header class="bg-gray-50 p-4">
                <div>@include('layouts.navigation')</div>
                @if (isset($header))
                <div>{{ $header }}</div>
                @endif
            </header>

            <!-- Page Content -->
            <main>{{ $slot }}</main>

            <!-- Page Footer -->
            <footer>
                <div class="flex justify-end bg-gray-900 py-1 text-gray-100">
                    <address class="px-4">&copy 2007 haessal</address>
                </div>
            </footer>
        </div>
    </body>
</html>
