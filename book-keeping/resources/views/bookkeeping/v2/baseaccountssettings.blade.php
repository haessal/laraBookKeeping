@extends('bookkeeping.v2.baseaccounts')

@section('v2_page_accounts_content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-3 bg-light">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" action="{{ route('v2_accounts_settings_redirect', ['bookId' => $book['id']]) }}">
                            @csrf
                            <div class="form-group">
                                <div class="row py-1">{{{ __('Accounts Group') }}}</div>
                                <div class="row py-1">
                                    <select name="accountsgroup" class="form-control">
                                        <option value="0" selected></option>
                                        @foreach ($accountstitle['groups'] as $accountsGroupId => $accountsGroup)
                                        <option value="{{ $accountsGroupId }}">{{{ $accountsGroup }}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row py-1">
                                    <button type="submit" class="btn btn-primary">{{{ __('Select') }}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <form method="POST" action="{{ route('v2_accounts_settings_redirect', ['bookId' => $book['id']]) }}">
                            @csrf
                            <div class="form-group">
                                <div class="row py-1">{{{ __('Accounts Item') }}}</div>
                                <div class="row py-1">
                                    <select name="accountsitem" class="form-control">
                                        <option value="0" selected></option>
                                        @foreach ($accountstitle['items'] as $accountsItemId => $accountsItem)
                                        <option value="{{ $accountsItemId }}">{{{ $accountsItem }}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row py-1">
                                    <button type="submit" class="btn btn-primary">{{{ __('Select') }}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-9 rounded border">
            <div class="container-fluid">
                @yield('v2_page_accounts_settings_content')
            </div>
        </div>
    </div>
</div>
@endsection
