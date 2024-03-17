<div class="container mx-auto px-8 sm:px-20">
    <div class="my-5 border border-gray-200 p-2 dark:border-gray-700">
        <ul class="flex justify-around">
            <x-bookkeeping.accounts-menu-item
                :bookId="$bookId"
                :selectedaccountsmenu="$selectedaccountsmenu"
                accountsmenuname="accounts_list"
                linkname="v2_accounts">
                {{ __('List') }}
            </x-bookkeeping.accounts-menu-item>
            <x-bookkeeping.accounts-menu-item
                :bookId="$bookId"
                :selectedaccountsmenu="$selectedaccountsmenu"
                accountsmenuname="accounts_add"
                linkname="v2_accounts_new">
                {{ __('Add') }}
            </x-bookkeeping.accounts-menu-item>
            <x-bookkeeping.accounts-menu-item
                :bookId="$bookId"
                :selectedaccountsmenu="$selectedaccountsmenu"
                accountsmenuname="accounts_settings"
                linkname="v2_accounts_settings">
                {{ __('Advanced Setting') }}
            </x-bookkeeping.accounts-menu-item>
        </ul>
    </div>
</div>
