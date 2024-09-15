<x-app-layout>
    <x-slot name="title">{{ __('Default Book') }}</x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-6 md:px-8">
            <h2 class="py-3 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Default Book') }}
            </h2>
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div
                    class="border-b border-gray-200 bg-white p-6 text-black dark:border-gray-900 dark:bg-gray-800 dark:text-gray-200">
                    @if (is_null($defaultBook)) {{{ $message }}}
                    <br />
                    <br />
                    <form method="POST" action="{{ route('settings_default_book') }}">
                        @csrf
                        <div class="my-0 mb-3">
                            <select
                                id="id-settings-default-book-select-book"
                                name="selectedBook"
                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-gray-400 dark:focus:ring-gray-400">
                                <option value="0" selected></option>
                                @foreach ($candidates as $candidate)
                                <option value="{{ $candidate['bookId'] }}">{{{ $candidate['bookName'] }}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mx-3 flex flex-row-reverse">
                            <x-primary-button type="submit" class="">{{ __('Set') }}</x-primary-button>
                        </div>
                    </form>
                    @else {{{ $defaultBook['name'] }}}
                    <br />
                    <br />
                    <form method="POST" action="{{ route('settings_default_book') }}">
                        @csrf
                        <input name="_method" type="hidden" value="DELETE" />
                        <x-primary-button type="submit">{{ __('Remove from the default') }}</x-primary-button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
