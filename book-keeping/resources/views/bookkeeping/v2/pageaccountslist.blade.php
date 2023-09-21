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
                    <x-bookkeeping.accountslist-root>{{ __('Assets') }}</x-bookkeeping.accountslist-root>
                    @empty($accounts['asset'])
                    <div class="my-3">
                        <div class="mx-3 italic text-black dark:text-gray-200">{{ __('There is no items.') }}</div>
                    </div>
                    @else @foreach ($accounts['asset'] as $accountsGroupId => $accountsGroup)
                    <div class="my-3 rounded-md border border-gray-200 dark:border-gray-700">
                        <x-bookkeeping.accountslist-group :bookId="$bookId" :accountsGroupId="$accountsGroupId">
                            {{{ $accountsGroup['title'] }}}
                        </x-bookkeeping.accountslist-group>
                        @empty($accountsGroup['items'])
                        <div class="m-4 bg-white text-black dark:bg-gray-800 dark:text-gray-200"></div>
                        @else
                        <div
                            class="m-4 rounded-md border border-gray-200 bg-white dark:border-gray-900 dark:bg-gray-800">
                            <div class="flow-root text-black dark:text-gray-200">
                                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-900">
                                    @foreach ($accountsGroup['items'] as $accountsItemId => $accountsItem)
                                    <x-bookkeeping.accountslist-item
                                        :bookId="$bookId"
                                        :accountsItemId="$accountsItemId"
                                        :selectable="$accountsItem['selectable']">
                                        {{{ $accountsItem['title'] }}}
                                    </x-bookkeeping.accountslist-item>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endempty
                    </div>
                    @endforeach @endempty
                </div>
                <div class="py-3">
                    <x-bookkeeping.accountslist-root>{{ __('Liabilities') }}</x-bookkeeping.accountslist-root>
                    @empty($accounts['liability'])
                    <div class="my-3">
                        <div class="mx-3 italic text-black dark:text-gray-200">{{ __('There is no items.') }}</div>
                    </div>
                    @else @foreach ($accounts['liability'] as $accountsGroupId => $accountsGroup)
                    <div class="my-3 rounded-md border border-gray-200 dark:border-gray-700">
                        <x-bookkeeping.accountslist-group :bookId="$bookId" :accountsGroupId="$accountsGroupId">
                            {{{ $accountsGroup['title'] }}}
                        </x-bookkeeping.accountslist-group>
                        @empty($accountsGroup['items'])
                        <div class="m-4 bg-white text-black dark:bg-gray-800 dark:text-gray-200"></div>
                        @else
                        <div
                            class="m-4 rounded-md border border-gray-200 bg-white dark:border-gray-900 dark:bg-gray-800">
                            <div class="flow-root text-black dark:text-gray-200">
                                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-900">
                                    @foreach ($accountsGroup['items'] as $accountsItemId => $accountsItem)
                                    <x-bookkeeping.accountslist-item
                                        :bookId="$bookId"
                                        :accountsItemId="$accountsItemId"
                                        :selectable="$accountsItem['selectable']">
                                        {{{ $accountsItem['title'] }}}
                                    </x-bookkeeping.accountslist-item>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endempty
                    </div>
                    @endforeach @endempty
                </div>
                <div class="py-3">
                    <x-bookkeeping.accountslist-root>{{ __('Expense') }}</x-bookkeeping.accountslist-root>
                    @empty($accounts['expense'])
                    <div class="my-3">
                        <div class="mx-3 italic text-black dark:text-gray-200">{{ __('There is no items.') }}</div>
                    </div>
                    @else @foreach ($accounts['expense'] as $accountsGroupId => $accountsGroup)
                    <div class="my-3 rounded-md border border-gray-200 dark:border-gray-700">
                        <x-bookkeeping.accountslist-group :bookId="$bookId" :accountsGroupId="$accountsGroupId">
                            {{{ $accountsGroup['title'] }}}
                        </x-bookkeeping.accountslist-group>
                        @empty($accountsGroup['items'])
                        <div class="m-4 bg-white text-black dark:bg-gray-800 dark:text-gray-200"></div>
                        @else
                        <div
                            class="m-4 rounded-md border border-gray-200 bg-white dark:border-gray-900 dark:bg-gray-800">
                            <div class="flow-root text-black dark:text-gray-200">
                                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-900">
                                    @foreach ($accountsGroup['items'] as $accountsItemId => $accountsItem)
                                    <x-bookkeeping.accountslist-item
                                        :bookId="$bookId"
                                        :accountsItemId="$accountsItemId"
                                        :selectable="$accountsItem['selectable']">
                                        {{{ $accountsItem['title'] }}}
                                    </x-bookkeeping.accountslist-item>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endempty
                    </div>
                    @endforeach @endempty
                </div>
                <div class="py-3">
                    <x-bookkeeping.accountslist-root>{{ __('Revenue') }}</x-bookkeeping.accountslist-root>
                    @empty($accounts['revenue'])
                    <div class="my-3">
                        <div class="mx-3 italic text-black dark:text-gray-200">{{ __('There is no items.') }}</div>
                    </div>
                    @else @foreach ($accounts['revenue'] as $accountsGroupId => $accountsGroup)
                    <div class="my-3 rounded-md border border-gray-200 dark:border-gray-700">
                        <x-bookkeeping.accountslist-group :bookId="$bookId" :accountsGroupId="$accountsGroupId">
                            {{{ $accountsGroup['title'] }}}
                        </x-bookkeeping.accountslist-group>
                        @empty($accountsGroup['items'])
                        <div class="m-4 bg-white text-black dark:bg-gray-800 dark:text-gray-200"></div>
                        @else
                        <div
                            class="m-4 rounded-md border border-gray-200 bg-white dark:border-gray-900 dark:bg-gray-800">
                            <div class="flow-root text-black dark:text-gray-200">
                                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-900">
                                    @foreach ($accountsGroup['items'] as $accountsItemId => $accountsItem)
                                    <x-bookkeeping.accountslist-item
                                        :bookId="$bookId"
                                        :accountsItemId="$accountsItemId"
                                        :selectable="$accountsItem['selectable']">
                                        {{{ $accountsItem['title'] }}}
                                    </x-bookkeeping.accountslist-item>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endempty
                    </div>
                    @endforeach @endempty
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
