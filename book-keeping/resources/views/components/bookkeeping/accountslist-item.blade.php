<li class="flex py-3 px-4">
    <div class="flex-1">
        <a
            href="{{  route('v2_accounts_items', ['bookId' => $bookId, 'accountsItemId' => $accountsItemId])  }}"
            class="flex-1 hover:underline">
            {{ $slot }}
        </a>
    </div>
    @if ($selectable)
    <div class="flex-none">
        <p class="rounded-xl border px-2 text-sm text-gray-500 dark:text-gray-400">{{ __('selectable') }}</p>
    </div>
    @endif
</li>
