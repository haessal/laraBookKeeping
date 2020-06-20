@extends('bookkeeping.v2.baseaccounts')

@section('v2_page_accounts_content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="py-3">
            <div class="h3">{{{ __('Assets') }}}</div>
            <hr>
            @isset($accounts['asset'])
            @foreach ($accounts['asset'] as $accountsGroupId => $accountsGroup)
            @include('bookkeeping.v2.accountsline')
            @endforeach
            @endisset
        </div>
        <div class="py-3">
            <div class="h3">{{{ __('Liabilities') }}}</div>
            <hr>
            @isset($accounts['liability'])
            @foreach ($accounts['liability'] as $accountsGroupId => $accountsGroup)
            @include('bookkeeping.v2.accountsline')
            @endforeach
            @endisset
        </div>
        <div class="py-3">
            <div class="h3">{{{ __('Expense') }}}</div>
            <hr>
            @isset($accounts['expense'])
            @foreach ($accounts['expense'] as $accountsGroupId => $accountsGroup)
            @include('bookkeeping.v2.accountsline')
            @endforeach
            @endisset
        </div>
        <div class="py-3">
            <div class="h3">{{{ __('Revenue') }}}</div>
            <hr>
            @isset($accounts['revenue'])
            @foreach ($accounts['revenue'] as $accountsGroupId => $accountsGroup)
            @include('bookkeeping.v2.accountsline')
            @endforeach
            @endisset
        </div>
    </div>
</div>
@endsection
