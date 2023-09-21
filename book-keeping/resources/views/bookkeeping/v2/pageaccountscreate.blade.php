<x-app-layout>
    <x-slot name="title">{{ __('Accounts') }}</x-slot>
    <div>
        <x-bookkeeping.book-menu :bookId="$bookId" :selectedlink="$selflinkname">
            {{{ $book['owner'] }}} / {{{ $book['name'] }}}
        </x-bookkeeping.book-menu>
        <x-bookkeeping.accounts-menu :bookId="$bookId" :selectedaccountsmenu="$selfaccountsmenu" />
        <div class="container mx-auto">
            <div class="my-3 px-3 py-1">
                <div class="py-3">
                    <h2 class="pb-1 text-xl text-black dark:text-gray-200">{{ __('Add Account Item') }}</h2>
                    <div
                        class="border border-gray-200 bg-white p-2 shadow-sm dark:border-gray-900 dark:bg-gray-800 sm:rounded-lg">
                        <form method="POST" action="{{ route('v2_accounts_new', ['bookId' => $book['id']]) }}">
                            @csrf
                            <div class="mx-3 mt-3 mb-6">
                                <x-bookkeeping.accountscreate-label for="accountgroup">
                                    {{ __('Accounts Group') }}
                                </x-bookkeeping.accountscreate-label>
                                <x-bookkeeping.accountscreate-select id="accountgroup" name="accountgroup">
                                    <option value="0"></option>
                                    @empty ($accountstitle) @else @foreach ($accountstitle as $accountGroupKey =>
                                    $accountGroup) @if ($accountcreate['groupid'] == $accountGroupKey)
                                    <option value="{{ $accountGroupKey }}" selected>{{{ $accountGroup }}}</option>
                                    @else
                                    <option value="{{ $accountGroupKey }}">{{{ $accountGroup }}}</option>
                                    @endif @endforeach @endempty
                                </x-bookkeeping.accountscreate-select>
                            </div>
                            <div class="mx-3 mt-3 mb-6">
                                <x-bookkeeping.accountscreate-label for="item-title">
                                    {{ __('Name') }}
                                </x-bookkeeping.accountscreate-label>
                                <x-bookkeeping.accountscreate-input id="item-title" name="title">
                                    {{{ $accountcreate['itemtitle'] }}}
                                </x-bookkeeping.accountscreate-input>
                            </div>
                            <div class="mx-3 mt-3 mb-6">
                                <x-bookkeeping.accountscreate-label for="description">
                                    {{ __('Description') }}
                                </x-bookkeeping.accountscreate-label>
                                <x-bookkeeping.accountscreate-textarea id="description" name="description">
                                    {{{ $accountcreate['description'] }}}
                                </x-bookkeeping.accountscreate-textarea>
                            </div>
                            <div class="mx-3 flex flex-row-reverse">
                                <x-bookkeeping.accountscreate-submit name="create" value="item">
                                    {{ __('Add') }}
                                </x-bookkeeping.accountscreate-submit>
                            </div>
                        </form>
                        @isset($messages['item'])
                        <x-bookkeeping.accountscreate-message>
                            {{{ $messages['item'] }}}
                        </x-bookkeeping.accountscreate-message>
                        @endisset
                    </div>
                </div>
                <div class="py-3">
                    <h2 class="pb-1 text-xl text-black dark:text-gray-200">{{ __('Accounts Group') }}</h2>
                    <div
                        class="border border-gray-200 bg-white p-2 shadow-sm dark:border-gray-900 dark:bg-gray-800 sm:rounded-lg">
                        <form method="POST" action="{{ route('v2_accounts_new', ['bookId' => $book['id']]) }}">
                            @csrf
                            <fieldset class="mx-3 mt-3 mb-6">
                                <legend class="mb-2 block text-black dark:text-gray-200">{{ __('Type') }}</legend>
                                <div class="flex flex-col sm:mb-4 sm:flex-row">
                                    <x-bookkeeping.accountscreate-radio
                                        id="radio_md_asset"
                                        name="accounttype"
                                        value="asset"
                                        checked="{{ $accounttype['asset'] }}">
                                        {{ __('Assets') }}
                                    </x-bookkeeping.accountscreate-radio>
                                    <x-bookkeeping.accountscreate-radio
                                        id="radio_md_liability"
                                        type="radio"
                                        name="accounttype"
                                        value="liability"
                                        checked="{{ $accounttype['liability'] }}">
                                        {{ __('Liabilities') }}
                                    </x-bookkeeping.accountscreate-radio>
                                    <x-bookkeeping.accountscreate-radio
                                        id="radio_md_expense"
                                        type="radio"
                                        name="accounttype"
                                        value="expense"
                                        checked="{{ $accounttype['expense'] }}">
                                        {{ __('Expense') }}
                                    </x-bookkeeping.accountscreate-radio>
                                    <x-bookkeeping.accountscreate-radio
                                        id="radio_md_revenue"
                                        type="radio"
                                        name="accounttype"
                                        value="revenue"
                                        checked="{{ $accounttype['revenue'] }}">
                                        {{ __('Revenue') }}
                                    </x-bookkeeping.accountscreate-radio>
                                </div>
                            </fieldset>
                            <div class="mx-3 mt-3 mb-6">
                                <x-bookkeeping.accountscreate-label for="group-title">
                                    {{ __('Name') }}
                                </x-bookkeeping.accountscreate-label>
                                <x-bookkeeping.accountscreate-input id="group-title" name="title">
                                    {{{ $accountcreate['grouptitle'] }}}
                                </x-bookkeeping.accountscreate-input>
                            </div>
                            <div class="mx-3 flex flex-row-reverse">
                                <x-bookkeeping.accountscreate-submit name="create" value="group">
                                    {{ __('Add') }}
                                </x-bookkeeping.accountscreate-submit>
                            </div>
                        </form>
                        @isset($messages['group'])
                        <x-bookkeeping.accountscreate-message>
                            {{{ $messages['group'] }}}
                        </x-bookkeeping.accountscreate-message>
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
