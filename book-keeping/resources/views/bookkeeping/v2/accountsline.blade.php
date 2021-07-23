<div class="card mb-2">
    <div class="card-header">
        <span class="card-link">
            <div class="text-dark">{{{ $accountsGroup['title'] }}}</div>
        </span>
    </div>
    <div class="card-body">
        @isset($accountsGroup['items'])
        <div class="list-group">
            @foreach ($accountsGroup['items'] as $accountsItemId => $accountsItem)
            @if ($accountsItem['selectable'] == 1)
            <span class="list-group-item list-group-item-light">
                <div class="text-dark">{{{ $accountsItem['title'] }}}</div>
            </span>
            @else
            <span class="list-group-item list-group-item-secondary">
                <div class="text-dark">{{{ $accountsItem['title'] }}}</div>
            </span>
            @endif
            @endforeach
        </div>
        @endisset
    </div>
</div>
