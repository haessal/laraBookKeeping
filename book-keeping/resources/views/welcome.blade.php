<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>Welcome to BookKeeping</title>

        <!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('/favicon.ico') }}" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white dark:bg-black">
        <header class="bg-gray-50 p-4 dark:bg-gray-900">
            <nav class="container mx-auto flex items-center justify-between">
                <a href="{{ url('/') }}" class="flex items-end">
                    <img src="{{ asset('/bookkeeping-logo.svg') }}" class="h-10 w-10" alt="BookKeeping Logo" />
                    <span class="px-1 text-xl text-gray-900 dark:text-gray-400 md:text-3xl">BookKeeping</span>
                </a>
                <div class="text-gray-900 dark:text-gray-400">
                    @auth
                    <a
                        href="{{ url('/dashboard') }}"
                        class="mx-1 rounded bg-gray-200 py-2 px-2 text-sm text-gray-900 duration-200 hover:bg-gray-800 hover:text-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-300 dark:hover:text-gray-800 md:px-4 md:text-base">
                        Dashboard
                    </a>
                    @else
                    <a
                        href="{{ route('login') }}"
                        class="mx-1 rounded bg-gray-200 py-2 px-2 text-sm text-gray-900 duration-200 hover:bg-gray-800 hover:text-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-300 dark:hover:text-gray-800 md:px-4 md:text-base">
                        Log in
                    </a>
                    @if (Route::has('register'))
                    <a
                        href="{{ route('register') }}"
                        class="mx-1 rounded border-2 border-black bg-gray-200 py-2 px-2 text-sm text-gray-900 duration-200 hover:bg-gray-800 hover:text-gray-100 dark:border-white dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-300 dark:hover:text-gray-800 md:px-4 md:text-base">
                        Register
                    </a>
                    @endif @endauth
                </div>
            </nav>
        </header>
        <main>
            <div class="container mx-auto flex px-10 py-40">
                <h1 class="text-center text-4xl font-semibold text-black dark:text-gray-200">
                    Keep your household account book by double entry.
                </h1>
            </div>
        </main>
        <footer>
            <div class="flex justify-end bg-gray-900 py-1 text-gray-100 dark:bg-gray-700 dark:text-gray-400">
                <address class="px-4">&copy 2007 haessal</address>
            </div>
        </footer>
    </body>
</html>
