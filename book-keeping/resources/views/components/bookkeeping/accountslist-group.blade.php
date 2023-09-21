<h3 class="rounded-t-md bg-gray-100 px-4 py-3 text-black dark:bg-gray-700 dark:text-gray-200">
    <a
        href="{{ route('v2_accounts_groups', ['bookId' => $bookId, 'accountsGroupId' => $accountsGroupId]) }}"
        class="hover:underline">
        {{ $slot }}
    </a>
</h3>
<hr class="border-gray-200 dark:border-gray-700" />
