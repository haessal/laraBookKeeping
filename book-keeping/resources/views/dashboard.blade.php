<x-app-layout>
    <x-slot name="title">{{ __('Dashboard') }}</x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div
                    class="border-b border-gray-200 bg-white p-6 text-black dark:border-gray-900 dark:bg-gray-800 dark:text-gray-200">
                    {{ __("You're logged in!") }}
                </div>
            </div>
            <h2 class="py-3 pl-2 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 sm:pl-0">
                {{ __('Book List') }}
            </h2>
            <div
                class="border border-gray-200 bg-white p-2 shadow-sm dark:border-gray-900 dark:bg-gray-800 sm:rounded-lg">
                @isset($book_list)
                <div class="flow-root text-black dark:text-gray-200">
                    <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-900">
                        @foreach ($book_list as $book)
                        <li class="py-3 sm:py-4">
                            <div class="flex items-center space-x-4 px-4">
                                <div class="flex-1">
                                    @isset($v2_book_page)
                                    <a
                                        href="{{ route($v2_book_page, ['bookId' => $book['id']]) }}"
                                        class="hover:underline">
                                        {{{ $book['owner'] }}} / {{{ $book['name'] }}}
                                    </a>
                                    @else
                                    <p>{{{ $book['owner'] }}} / {{{ $book['name'] }}}</p>
                                    @endisset
                                </div>
                                @if ($book['is_default'])
                                <div class="flex-none">
                                    <p class="rounded-xl border px-2 text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('Default') }}
                                    </p>
                                </div>
                                @endif @if (! $book['modifiable'])
                                <div class="flex-none">
                                    <p class="rounded-xl border px-2 text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('Read Only') }}
                                    </p>
                                </div>
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @else
                <div class="bg-white p-4 text-black dark:bg-gray-800 dark:text-gray-200">
                    {{ __("You don't have any books yet.") }}
                </div>
                @endisset
            </div>
        </div>
    </div>
</x-app-layout>
