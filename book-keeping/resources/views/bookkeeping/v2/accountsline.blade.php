<div class="card mb-2">
    <div class="card-header">
        <a
            href="{{ route('v2_accounts_groups', ['bookId' => $book['id'], 'accountsGroupId' => $accountsGroupId]) }}"
            class="card-link">
            <div class="text-dark">{{{ $accountsGroup['title'] }}}</div>
        </a>
    </div>
    <div class="card-body">
        @isset($accountsGroup['items'])
        <div class="list-group">
            @foreach ($accountsGroup['items'] as $accountsItemId => $accountsItem) @if ($accountsItem['selectable'] ==
            1)
            <a
                href="{{ route('v2_accounts_items', ['bookId' => $book['id'], 'accountsItemId' => $accountsItemId]) }}"
                class="list-group-item list-group-item-light">
                <div class="text-dark">{{{ $accountsItem['title'] }}}</div>
            </a>
            @else
            <a
                href="{{ route('v2_accounts_items', ['bookId' => $book['id'], 'accountsItemId' => $accountsItemId]) }}"
                class="list-group-item list-group-item-secondary">
                <div class="text-dark">{{{ $accountsItem['title'] }}}</div>
            </a>
            @endif @endforeach
        </div>
        @endisset
    </div>
</div>
