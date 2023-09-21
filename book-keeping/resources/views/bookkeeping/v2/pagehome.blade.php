<x-app-layout>
    <x-slot name="title">{{ __('Home') }}</x-slot>

    <div>
        <x-bookkeeping.book-menu :bookId="$bookId" :selectedlink="$selflinkname">
            {{{ $book['owner'] }}} / {{{ $book['name'] }}}
        </x-bookkeeping.book-menu>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div
                        class="border-b border-gray-200 bg-white p-6 text-black dark:border-gray-900 dark:bg-gray-800 dark:text-gray-200">
                        {{ __('Coming Soon...') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
