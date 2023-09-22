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
                                <x-bookkeeping.accountssettings-menu-select
                                    id="id-accounts-settings-menu-select-accountsgroup"
                                    name="accountsgroup"
                                    title="{{ __('Accounts Group') }}">
                                    <option value="0" selected></option>
                                    @foreach($accountstitle['groups'] as $accountsGroupId => $accountsGroup)
                                    <option value="{{ $accountsGroupId }}">{{{ $accountsGroup }}}</option>
                                    @endforeach
                                </x-bookkeeping.accountssettings-menu-select>
                                <x-bookkeeping.accountssettings-menu-submit>
                                    {{ __('Select') }}
                                </x-bookkeeping.accountssettings-menu-submit>
                            </div>
                        </form>
                    </div>
                    <div class="mx-2 my-1 w-full sm:w-1/2">
                        <form
                            method="POST"
                            action="{{ route('v2_accounts_settings_redirect', ['bookId' => $bookId]) }}">
                            @csrf
                            <div class="flex flex-col sm:flex-row sm:items-center">
                                <x-bookkeeping.accountssettings-menu-select
                                    id="id-accounts-settings-menu-select-accountsitem"
                                    name="accountsitem"
                                    title="{{ __('Accounts Item') }}">
                                    <option value="0" selected></option>
                                    @foreach($accountstitle['items'] as $accountsItemId => $accountsItem)
                                    <option value="{{ $accountsItemId }}">{{{ $accountsItem }}}</option>
                                    @endforeach
                                </x-bookkeeping.accountssettings-menu-select>
                                <x-bookkeeping.accountssettings-menu-submit>
                                    {{ __('Select') }}
                                </x-bookkeeping.accountssettings-menu-submit>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto">
            @isset($accountsgroup)
            <div class="px-3 py-1">
                <x-bookkeeping.accounts-form caption="{{ __('Edit Account Group') }}">
                    <form
                        method="POST"
                        action="{{ route('v2_accounts_groups', ['bookId' => $bookId, 'accountsGroupId' => $accountsgroup['id']]) }}">
                        @csrf
                        <x-bookkeeping.accounts-form-display title="{{ __('Type') }}">
                            {{{ $accountsgroup['type'] }}}
                        </x-bookkeeping.accounts-form-display>
                        <x-bookkeeping.accounts-form-textbox
                            id="id-accounts-settings-textbox-accountsgroup-title"
                            name="title"
                            title="{{ __('Name') }}">
                            {{{ $accountsgroup['title'] }}}
                        </x-bookkeeping.accounts-form-textbox>
                        <x-bookkeeping.accounts-form-checkboxes title="{{ __('Attributes') }}">
                            <x-bookkeeping.accounts-form-checkbox
                                id="id-accounts-settings-checkbox-current"
                                name="attribute_current"
                                checked="{{ $accountsgroup['attribute_current'] }}">
                                {{ __('Has Liquidity') }}
                            </x-bookkeeping.accounts-form-checkbox>
                        </x-bookkeeping.accounts-form-checkboxes>
                        <x-bookkeeping.accounts-form-display title="{{ __('Code for Previous version') }}">
                            {{{ $accountsgroup['bk_code'] }}}
                        </x-bookkeeping.accounts-form-display>
                        <x-bookkeeping.accounts-form-submit name="update" value="update">
                            {{ __('Update') }}
                        </x-bookkeeping.accounts-form-submit>
                    </form>
                    @isset($message)
                    <x-bookkeeping.accounts-form-message>{{{ $message }}}</x-bookkeeping.accounts-form-message>
                    @endisset
                </x-bookkeeping.accounts-form>
            </div>
            @endisset @isset($accountsitem)
            <div class="px-3 py-1">
                <x-bookkeeping.accounts-form caption="{{ __('Edit Account Item') }}">
                    <form
                        method="POST"
                        action="{{ route('v2_accounts_items', ['bookId' => $bookId, 'accountsItemId' => $accountsitem['id']]) }}">
                        @csrf
                        <x-bookkeeping.accounts-form-display title="{{ __('Type') }}">
                            {{{ $accountsitem['type'] }}}
                        </x-bookkeeping.accounts-form-display>
                        <x-bookkeeping.accounts-form-select
                            id="id-accounts-settings-select-accountsgroup"
                            name="accountgroup"
                            title="{{ __('Accounts Group') }}">
                            @foreach($accountsgroups as $accountGroupKey => $accountGroup) @if($accountsitem['groupid']
                            == $accountGroupKey)
                            <option value="{{ $accountGroupKey }}" selected>{{{ $accountGroup }}}</option>
                            @else
                            <option value="{{ $accountGroupKey }}">{{{ $accountGroup }}}</option>
                            @endif @endforeach
                        </x-bookkeeping.accounts-form-select>
                        <x-bookkeeping.accounts-form-textbox
                            id="id-accounts-settings-textbox-accountsitem-title"
                            name="title"
                            title="{{ __('Name') }}">
                            {{{ $accountsitem['title'] }}}
                        </x-bookkeeping.accounts-form-textbox>
                        <x-bookkeeping.accounts-form-textarea
                            id="id-accounts-settings-textarea-accountsitem-description"
                            name="description"
                            rows="3"
                            title="{{ __('Description') }}">
                            {{{ $accountsitem['description'] }}}
                        </x-bookkeeping.accounts-form-textarea>
                        <x-bookkeeping.accounts-form-checkboxes title="{{ __('Attributes') }}">
                            <x-bookkeeping.accounts-form-checkbox
                                id="id-accounts-settings-checkbox-selectable"
                                name="attribute_selectable"
                                checked="{{ $accountsitem['attribute_selectable'] }}">
                                {{ __('Selectable') }}
                            </x-bookkeeping.accounts-form-checkbox>
                        </x-bookkeeping.accounts-form-checkboxes>
                        <x-bookkeeping.accounts-form-display title="{{ __('Code for Previous version') }}">
                            {{{ $accountsitem['bk_code'] }}}
                        </x-bookkeeping.accounts-form-display>
                        <x-bookkeeping.accounts-form-submit name="update" value="update">
                            {{ __('Update') }}
                        </x-bookkeeping.accounts-form-submit>
                    </form>
                    @isset($message)
                    <x-bookkeeping.accounts-form-message>{{{ $message }}}</x-bookkeeping.accounts-form-message>
                    @endisset
                </x-bookkeeping.accounts-form>
            </div>
            @endisset @if (!(isset($accountsgroup) || isset($accountsitem)))
            <div class="px-3 py-1">
                <x-bookkeeping.accounts-form-empty>
                    {{ __('Select Accounts Group or Item.') }}
                </x-bookkeeping.accounts-form-empty>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
