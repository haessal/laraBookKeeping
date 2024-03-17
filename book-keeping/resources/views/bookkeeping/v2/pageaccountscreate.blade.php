<x-app-layout>
    <x-slot name="title">{{ __('Accounts') }}</x-slot>
    <div>
        <x-bookkeeping.book-menu :bookId="$bookId" :selectedlink="$selflinkname">
            {{{ $book['owner'] }}} / {{{ $book['name'] }}}
        </x-bookkeeping.book-menu>
        <x-bookkeeping.accounts-menu :bookId="$bookId" :selectedaccountsmenu="$selfaccountsmenu" />
        <div class="container mx-auto">
            <div class="my-3 px-3 py-1">
                <x-bookkeeping.accounts-form caption="{{ __('Add Account Item') }}">
                    <form method="POST" action="{{ route('v2_accounts_new', ['bookId' => $book['id']]) }}">
                        @csrf
                        <x-bookkeeping.accounts-form-select
                            id="id-accounts-create-select-accountsgroup"
                            name="accountgroup"
                            title="{{ __('Accounts Group') }}">
                            <option value="0"></option>
                            @empty($accountstitle) @else @foreach($accountstitle as $accountGroupKey => $accountGroup)
                            @if($accountcreate['groupId'] == $accountGroupKey)
                            <option value="{{ $accountGroupKey }}" selected>{{{ $accountGroup }}}</option>
                            @else
                            <option value="{{ $accountGroupKey }}">{{{ $accountGroup }}}</option>
                            @endif @endforeach @endempty
                        </x-bookkeeping.accounts-form-select>
                        <x-bookkeeping.accounts-form-textbox
                            id="id-accounts-create-textbox-accountsitem-title"
                            name="title"
                            title="{{ __('Name') }}">
                            {{{ $accountcreate['itemtitle'] }}}
                        </x-bookkeeping.accounts-form-textbox>
                        <x-bookkeeping.accounts-form-textarea
                            id="id-accounts-create-textarea-accountsitem-description"
                            name="description"
                            rows="3"
                            title="{{ __('Description') }}">
                            {{{ $accountcreate['description'] }}}
                        </x-bookkeeping.accounts-form-textarea>
                        <x-bookkeeping.accounts-form-submit name="create" value="item">
                            {{ __('Add') }}
                        </x-bookkeeping.accounts-form-submit>
                    </form>
                    @isset($messages['item'])
                    <x-bookkeeping.accounts-form-message>{{{ $messages['item'] }}}</x-bookkeeping.accounts-form-message>
                    @endisset
                </x-bookkeeping.accounts-form>
                <x-bookkeeping.accounts-form caption="{{ __('Add Account Group') }}">
                    <form method="POST" action="{{ route('v2_accounts_new', ['bookId' => $book['id']]) }}">
                        @csrf
                        <x-bookkeeping.accounts-form-radio-fieldset title="{{ __('Type') }}">
                            <x-bookkeeping.accounts-form-radio
                                id="id-accounts-create-radio-type-asset"
                                name="accounttype"
                                value="asset"
                                checked="{{ $accounttype['asset'] }}">
                                {{ __('Assets') }}
                            </x-bookkeeping.accounts-form-radio>
                            <x-bookkeeping.accounts-form-radio
                                id="id-accounts-create-radio-type-liability"
                                name="accounttype"
                                value="liability"
                                checked="{{ $accounttype['liability'] }}">
                                {{ __('Liabilities') }}
                            </x-bookkeeping.accounts-form-radio>
                            <x-bookkeeping.accounts-form-radio
                                id="id-accounts-create-radio-type-expense"
                                name="accounttype"
                                value="expense"
                                checked="{{ $accounttype['expense'] }}">
                                {{ __('Expense') }}
                            </x-bookkeeping.accounts-form-radio>
                            <x-bookkeeping.accounts-form-radio
                                id="id-accounts-create-radio-type-revenue"
                                name="accounttype"
                                value="revenue"
                                checked="{{ $accounttype['revenue'] }}">
                                {{ __('Revenue') }}
                            </x-bookkeeping.accounts-form-radio>
                        </x-bookkeeping.accounts-form-radio-fieldset>
                        <x-bookkeeping.accounts-form-textbox
                            id="id-accounts-create-textbox-accountsgroup-title"
                            name="title"
                            title="{{ __('Name') }}">
                            {{{ $accountcreate['grouptitle'] }}}
                        </x-bookkeeping.accounts-form-textbox>
                        <x-bookkeeping.accounts-form-submit name="create" value="group">
                            {{ __('Add') }}
                        </x-bookkeeping.accounts-form-submit>
                    </form>
                    @isset($messages['group'])
                    <x-bookkeeping.accounts-form-message>
                        {{{ $messages['group'] }}}
                    </x-bookkeeping.accounts-form-message>
                    @endisset
                </x-bookkeeping.accounts-form>
            </div>
        </div>
    </div>
</x-app-layout>
