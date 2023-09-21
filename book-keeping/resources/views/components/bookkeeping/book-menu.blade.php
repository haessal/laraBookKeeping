<div class="flex items-center justify-center bg-gray-50 pb-3 dark:bg-gray-900 md:pb-4">
    <p class="text-xl text-black dark:text-gray-200 md:text-2xl">{{ $slot }}</p>
</div>
<div
    class="border-b border-gray-200 bg-gray-50 px-4 text-center text-sm font-medium dark:border-gray-700 dark:bg-gray-900 sm:px-6 md:px-8 md:text-base lg:px-12 xl:px-20">
    <ul class="-mb-px flex flex-wrap">
        <x-bookkeeping.book-menu-item :bookId="$bookId" :selectedlink="$selectedlink" linkname="v2_home">
            {{ __('Home') }}
        </x-bookkeeping.book-menu-item>
        <x-bookkeeping.book-menu-item :bookId="$bookId" :selectedlink="$selectedlink" linkname="v2_accounts">
            {{ __('Accounts') }}
        </x-bookkeeping.book-menu-item>
        <x-bookkeeping.book-menu-item :bookId="$bookId" :selectedlink="$selectedlink" linkname="v2_settings">
            {{ __('Settings') }}
        </x-bookkeeping.book-menu-item>
    </ul>
</div>
