<nav class="container mx-auto flex items-center justify-between">
    <!-- Logo -->
    <a href="{{ url('/') }}" class="flex items-end">
        <img src="{{ asset('/bookkeeping-logo.svg') }}" class="h-10 w-10" alt="BookKeeping Logo" />
        <span class="px-1 text-xl text-gray-900 dark:text-gray-400 md:text-3xl">BookKeeping</span>
    </a>
    <!-- Primary Navigation Menu -->
    <div class="text-sm md:text-base">
        <div class="ml-6 flex items-center justify-between">
            <!-- Settings Dropdown -->
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button
                        class="flex items-center font-medium text-gray-500 transition duration-150 ease-in-out hover:border-gray-300 hover:text-gray-700 focus:border-gray-300 focus:text-gray-700 focus:outline-none dark:text-gray-400 dark:hover:text-gray-300 dark:focus:text-gray-300">
                        <div>login as {{ Auth::user()->name }}</div>
                        <!-- Icon of downward arrow -->
                        <div class="ml-1">
                            <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path
                                    fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link
                            :href="route('logout')"
                            onclick="event.preventDefault();
                                            this.closest('form').submit();"
                            class="border-t">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</nav>
