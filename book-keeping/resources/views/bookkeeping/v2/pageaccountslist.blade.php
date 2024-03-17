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
                    <x-bookkeeping.accountslist-group-empty>
                        {{ __('There are no items.') }}
                    </x-bookkeeping.accountslist-group-empty>
                    @else @foreach ($accounts['asset'] as $accountsGroupId => $accountsGroup)
                    <x-bookkeeping.accountslist-group
                        :bookId="$bookId"
                        :accountsGroupId="$accountsGroupId"
                        :title="$accountsGroup['title']">
                        @empty($accountsGroup['items'])
                        <x-bookkeeping.accountslist-items-empty />
                        @else
                        <x-bookkeeping.accountslist-items>
                            @foreach ($accountsGroup['items'] as $accountsItemId => $accountsItem)
                            <x-bookkeeping.accountslist-item
                                :bookId="$bookId"
                                :accountsItemId="$accountsItemId"
                                :selectable="$accountsItem['selectable']">
                                {{{ $accountsItem['title'] }}}
                            </x-bookkeeping.accountslist-item>
                            @endforeach
                        </x-bookkeeping.accountslist-items>
                        @endempty
                    </x-bookkeeping.accountslist-group>
                    @endforeach @endempty
                </div>
                <div class="py-3">
                    <x-bookkeeping.accountslist-root>{{ __('Liabilities') }}</x-bookkeeping.accountslist-root>
                    @empty($accounts['liability'])
                    <x-bookkeeping.accountslist-group-empty>
                        {{ __('There are no items.') }}
                    </x-bookkeeping.accountslist-group-empty>
                    @else @foreach ($accounts['liability'] as $accountsGroupId => $accountsGroup)
                    <x-bookkeeping.accountslist-group
                        :bookId="$bookId"
                        :accountsGroupId="$accountsGroupId"
                        :title="$accountsGroup['title']">
                        @empty($accountsGroup['items'])
                        <x-bookkeeping.accountslist-items-empty />
                        @else
                        <x-bookkeeping.accountslist-items>
                            @foreach ($accountsGroup['items'] as $accountsItemId => $accountsItem)
                            <x-bookkeeping.accountslist-item
                                :bookId="$bookId"
                                :accountsItemId="$accountsItemId"
                                :selectable="$accountsItem['selectable']">
                                {{{ $accountsItem['title'] }}}
                            </x-bookkeeping.accountslist-item>
                            @endforeach
                        </x-bookkeeping.accountslist-items>
                        @endempty
                    </x-bookkeeping.accountslist-group>
                    @endforeach @endempty
                </div>
                <div class="py-3">
                    <x-bookkeeping.accountslist-root>{{ __('Expense') }}</x-bookkeeping.accountslist-root>
                    @empty($accounts['expense'])
                    <x-bookkeeping.accountslist-group-empty>
                        {{ __('There are no items.') }}
                    </x-bookkeeping.accountslist-group-empty>
                    @else @foreach ($accounts['expense'] as $accountsGroupId => $accountsGroup)
                    <x-bookkeeping.accountslist-group
                        :bookId="$bookId"
                        :accountsGroupId="$accountsGroupId"
                        :title="$accountsGroup['title']">
                        @empty($accountsGroup['items'])
                        <x-bookkeeping.accountslist-items-empty />
                        @else
                        <x-bookkeeping.accountslist-items>
                            @foreach ($accountsGroup['items'] as $accountsItemId => $accountsItem)
                            <x-bookkeeping.accountslist-item
                                :bookId="$bookId"
                                :accountsItemId="$accountsItemId"
                                :selectable="$accountsItem['selectable']">
                                {{{ $accountsItem['title'] }}}
                            </x-bookkeeping.accountslist-item>
                            @endforeach
                        </x-bookkeeping.accountslist-items>
                        @endempty
                    </x-bookkeeping.accountslist-group>
                    @endforeach @endempty
                </div>
                <div class="py-3">
                    <x-bookkeeping.accountslist-root>{{ __('Revenue') }}</x-bookkeeping.accountslist-root>
                    @empty($accounts['revenue'])
                    <x-bookkeeping.accountslist-group-empty>
                        {{ __('There are no items.') }}
                    </x-bookkeeping.accountslist-group-empty>
                    @else @foreach ($accounts['revenue'] as $accountsGroupId => $accountsGroup)
                    <x-bookkeeping.accountslist-group
                        :bookId="$bookId"
                        :accountsGroupId="$accountsGroupId"
                        :title="$accountsGroup['title']">
                        @empty($accountsGroup['items'])
                        <x-bookkeeping.accountslist-items-empty />
                        @else
                        <x-bookkeeping.accountslist-items>
                            @foreach ($accountsGroup['items'] as $accountsItemId => $accountsItem)
                            <x-bookkeeping.accountslist-item
                                :bookId="$bookId"
                                :accountsItemId="$accountsItemId"
                                :selectable="$accountsItem['selectable']">
                                {{{ $accountsItem['title'] }}}
                            </x-bookkeeping.accountslist-item>
                            @endforeach
                        </x-bookkeeping.accountslist-items>
                        @endempty
                    </x-bookkeeping.accountslist-group>
                    @endforeach @endempty
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
