<x-app-layout>
    <x-slot name="title">{{ __('Accounts') }}</x-slot>
    <div>
        <x-bookkeeping.book-menu :bookId="$bookId" :selectedlink="$selflinkname">
            {{{ $book['owner'] }}} / {{{ $book['name'] }}}
        </x-bookkeeping.book-menu>
        <x-bookkeeping.accounts-menu :bookId="$bookId" :selectedaccountsmenu="$selfaccountsmenu" />
        <div class="container mx-auto mt-8">
            <div class="px-3">
                <div class="justify-normal flex flex-col sm:flex-row">
                    <div class="mx-2 my-1 w-full sm:w-1/2">
                        <form
                            method="POST"
                            action="{{ route('v2_accounts_settings_redirect', ['bookId' => $bookId]) }}">
                            @csrf
                            <div class="flex flex-col sm:flex-row sm:items-center">
                                <label class="p-1 text-black dark:text-gray-200">{{ __('Accounts Group') }}</label>
                                <select
                                    name="accountsgroup"
                                    class="w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-gray-400 dark:focus:ring-gray-400 sm:w-1/2">
                                    <option value="0" selected></option>
                                    @foreach ($accountstitle['groups'] as $accountsGroupId => $accountsGroup)
                                    <option value="{{ $accountsGroupId }}">{{{ $accountsGroup }}}</option>
                                    @endforeach
                                </select>
                                <div class="flex flex-row-reverse py-1 sm:p-1">
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-gray-800 px-5 py-2.5 text-center text-sm font-medium uppercase tracking-widest text-white duration-200 hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white dark:focus:bg-white dark:focus:ring-gray-200">
                                        {{ __('Select') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="mx-2 my-1 w-full sm:w-1/2">
                        <form
                            method="POST"
                            action="{{ route('v2_accounts_settings_redirect', ['bookId' => $bookId]) }}">
                            @csrf
                            <div class="flex flex-col sm:flex-row sm:items-center">
                                <label class="p-1 text-black dark:text-gray-200">{{ __('Accounts Item') }}</label>
                                <select
                                    name="accountsitem"
                                    class="w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-gray-400 dark:focus:ring-gray-400 sm:w-1/2">
                                    <option value="0" selected></option>
                                    @foreach ($accountstitle['items'] as $accountsItemId => $accountsItem)
                                    <option value="{{ $accountsItemId }}">{{{ $accountsItem }}}</option>
                                    @endforeach
                                </select>
                                <div class="flex flex-row-reverse py-1 sm:p-1">
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-gray-800 px-5 py-2.5 text-center text-sm font-medium uppercase tracking-widest text-white duration-200 hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white dark:focus:bg-white dark:focus:ring-gray-200">
                                        {{{ __('Select') }}}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto">
            @isset($accountsgroup)
            <div class="border border-red-600 px-3 py-1">
                <div class="py-3">
                    <h2 class="pb-1 text-xl text-black dark:text-gray-200">{{ __('Edit Account Group') }}</h2>
                    <div
                        class="border border-gray-200 bg-white p-2 shadow-sm dark:border-gray-900 dark:bg-gray-800 sm:rounded-lg">
                        <form
                            method="POST"
                            action="{{ route('v2_accounts_groups', ['bookId' => $bookId, 'accountsGroupId' => $accountsgroup['id']]) }}">
                            @csrf
                            <div class="mx-3 mt-3 mb-6">
                                <p class="mb-2 block text-black dark:text-gray-200">{{ __('Type') }}</p>
                                <p class="ml-2 block text-sm font-medium text-black dark:text-gray-200">
                                    {{{ $accountsgroup['type'] }}}
                                </p>
                            </div>
                            <div class="mx-3 mt-3 mb-6">
                                <p class="mb-2 block text-black dark:text-gray-200">{{ __('Name') }}</p>
                                <input
                                    type="text"
                                    name="title"
                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-gray-400 dark:focus:ring-gray-400"
                                    value="{{ $accountsgroup['title'] }}" />
                            </div>
                            <div class="mx-3 mt-3 mb-6">
                                <p class="mb-2 block text-black dark:text-gray-200">{{ __('Attributes') }}</p>
                                <div class="flex items-center">
                                    <input id="current" type="checkbox" name="attribute_current" value="1" class="ml-2
                                    h-4 w-4 border-gray-300 bg-gray-100 focus:ring-0 dark:border-gray-600
                                    dark:bg-gray-700" {{ $accountsgroup['attribute_current'] }} />
                                    <label
                                        for="current"
                                        class="ml-1 block text-sm font-medium text-black dark:text-gray-200">
                                        {{ __('Has Liquidity') }}
                                    </label>
                                </div>
                            </div>
                            <div class="mx-3 mt-3 mb-6">
                                <p class="mb-2 block text-black dark:text-gray-200">
                                    {{ __('Code for Previous version') }}
                                </p>
                                <p class="ml-2 block text-sm font-medium text-black dark:text-gray-200">
                                    {{{ $accountsgroup['bk_code'] }}}
                                </p>
                            </div>
                            <div class="mx-3 flex flex-row-reverse">
                                <x-bookkeeping.accountscreate-submit name="update" value="update">
                                    {{ __('Update') }}
                                </x-bookkeeping.accountscreate-submit>
                            </div>

                        </form>
                        @isset($message)
                        <x-bookkeeping.accountscreate-message>
                            {{{ $message }}}
                        </x-bookkeeping.accountscreate-message>
                        @endisset
                    </div>
                </div>
            </div>
            @endisset @isset($accountsitem)
            <div class="border border-red-600 px-3 py-1">
                <div class="py-3">
                    <h2 class="pb-1 text-xl text-black dark:text-gray-200">{{ __('Edit Account Item') }}</h2>
                    <div
                        class="border border-gray-200 bg-white p-2 shadow-sm dark:border-gray-900 dark:bg-gray-800 sm:rounded-lg">
                        <form
                            method="POST"
                            action="{{ route('v2_accounts_items', ['bookId' => $bookId, 'accountsItemId' => $accountsitem['id']]) }}">
                            @csrf
                            <div class="mx-3 mt-3 mb-6">
                                <p class="mb-2 block text-black dark:text-gray-200">{{ __('Type') }}</p>
                                <p class="ml-2 block text-sm font-medium text-black dark:text-gray-200">
                                    {{{ $accountsitem['type'] }}}
                                </p>
                            </div>
                            <div class="mx-3 mt-3 mb-6">
                                <p class="mb-2 block text-black dark:text-gray-200">{{ __('Accounts Group') }}</p>
                                <select name="accountgroup" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-gray-400 dark:focus:ring-gray-400">
                                    @foreach ($accountsgroups as $accountGroupKey => $accountGroup) 
                                    @if ($accountsitem['groupid'] == $accountGroupKey)
                                    <option value="{{ $accountGroupKey }}" selected>{{{ $accountGroup }}}</option>
                                    @else
                                    <option value="{{ $accountGroupKey }}">{{{ $accountGroup }}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="mx-3 mt-3 mb-6">
                                <p class="mb-2 block text-black dark:text-gray-200">{{ __('Name') }}</p>
                                <input
                                    type="text"
                                    name="title"
                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-gray-400 dark:focus:ring-gray-400"
                                    value="{{ $accountsitem['title'] }}" />
                            </div>
                            <div class="mx-3 mt-3 mb-6">
                                <p class="mb-2 block text-black dark:text-gray-200">{{ __('Description') }}</p>
                                <textarea rows="2" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-gray-900 focus:ring-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-gray-400 dark:focus:ring-gray-400" name="description">
{{{ $accountsitem['description'] }}}</textarea>
                            </div>
                            <div class="mx-3 mt-3 mb-6">
                                <p class="mb-2 block text-black dark:text-gray-200">{{ __('Attributes') }}</p>
                                <div class="flex items-center">
                                    <input id="md_selectable" type="checkbox" name="attribute_selectable" value="1" class="ml-2
                                    h-4 w-4 border-gray-300 bg-gray-100 focus:ring-0 dark:border-gray-600
                                    dark:bg-gray-700" {{ $accountsitem['attribute_selectable'] }} />
                                    <label
                                        for="md_selectable"
                                        class="ml-1 block text-sm font-medium text-black dark:text-gray-200">
                                        {{ __('Selectable') }}
                                    </label>
                                </div>
                            </div>
                            <div class="mx-3 mt-3 mb-6">
                                <p class="mb-2 block text-black dark:text-gray-200">
                                    {{ __('Code for Previous version') }}
                                </p>
                                <p class="ml-2 block text-sm font-medium text-black dark:text-gray-200">
                                    {{{ $accountsitem['bk_code'] }}}
                                </p>
                            </div>
                            <div class="mx-3 flex flex-row-reverse">
                                <x-bookkeeping.accountscreate-submit name="update" value="update">
                                    {{ __('Update') }}
                                </x-bookkeeping.accountscreate-submit>
                            </div>

                        </form>
                        @isset($message)
                        <x-bookkeeping.accountscreate-message>
                            {{{ $message }}}
                        </x-bookkeeping.accountscreate-message>
                        @endisset
                    </div>
                </div>
            </div>
            @endisset @if (!(isset($accountsgroup) || isset($accountsitem)))
            <div class="border border-red-600 px-3 py-1">
                <div class="py-3">
                    <div
                        class="flex justify-center border border-gray-200 bg-white p-2 shadow-sm dark:border-gray-900 dark:bg-gray-800 sm:rounded-lg">
                        <div class="italic text-black dark:text-gray-200">{{ __('Select Accounts Group or Item.') }} title</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
