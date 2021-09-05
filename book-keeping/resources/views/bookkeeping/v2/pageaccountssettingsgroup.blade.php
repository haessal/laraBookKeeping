@extends('bookkeeping.v2.baseaccountssettings')

@section('v2_page_accounts_settings_content')
<div class="h4 pt-3">{{{ __('Edit Account Group') }}}</div>
<hr>
<div class="container-fluid">
    @isset($accountsgroup)
    <div class="row d-none d-md-block">
        <form method="POST" action="{{ route('v2_accounts_groups', ['bookId' => $book['id'], 'accountsGroupId' => $accountsgroup['id']]) }}">
            @csrf
            <div class="form-group">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th class="table-active border border-secondary" width="25%">{{{ __('Type') }}}</th>
                            <td class="border border-secondary">{{{ $accountsgroup['type'] }}}</td>
                        </tr>
                        <tr>
                            <th class="table-active border border-secondary" width="25%">{{{ __('Name') }}}</th>
                            <td class="border border-secondary"><input type="text" class="form-control" name="title" value="{{ $accountsgroup['title'] }}"></td>
                        </tr>
                        <tr>
                            <th class="table-active border border-secondary" width="25%">{{{ __('Attributes') }}}</th>
                            <td class="border border-secondary">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="attribute_current" id="md_current" value="1" {{{ $accountsgroup['attribute_current'] }}}>
                                    <label class="form-check-label" for="md_current">{{{ __('Has Liquidity') }}}</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="table-active border border-secondary" width="25%">{{{ __('Code for Previous version') }}}</th>
                            <td class="border border-secondary">{{{ $accountsgroup['bk_code'] }}}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-right">
                    <button name="update" value="update" type="submit" class="btn btn-success">{{{ __('Update') }}}</button>
                </div>
            </div>
        </form>
    </div>
    <div class="row d-block d-md-none">
        <form method="POST" action="{{ route('v2_accounts_groups', ['bookId' => $book['id'], 'accountsGroupId' => $accountsgroup['id']]) }}">
            @csrf
            <div class="form-group">
                <table class="table table-bordered d-table d-md-none">
                    <tbody>
                        <tr><th class="table-active border border-secondary">{{{ __('Type') }}}</th></tr>
                        <tr><td class="border border-secondary">{{{ $accountsgroup['type'] }}}</td></tr>
                        <tr><th class="table-active border border-secondary">{{{ __('Name') }}}</th></tr>
                        <tr><td class="border border-secondary"><input type="text" class="form-control" name="title" value="{{ $accountsgroup['title'] }}"></td></tr>
                        <tr><th class="table-active border border-secondary">{{{ __('Attributes') }}}</th></tr>
                        <tr>
                            <td class="border border-secondary">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="attribute_current" id="current" value="1" {{{ $accountsgroup['attribute_current'] }}}>
                                    <label class="form-check-label" for="current">{{{ __('Has Liquidity') }}}</label>
                                </div>
                            </td>
                        </tr>
                        <tr><th class="table-active border border-secondary">{{{ __('Code for Previous version') }}}</th></tr>
                        <tr><td class="border border-secondary">{{{ $accountsgroup['bk_code'] }}}</td></tr>
                    </tbody>
                </table>
                <div class="text-right">
                    <button name="update" value="update" type="submit" class="btn btn-success">{{{ __('Update') }}}</button>
                </div>
                </div>
        </form>
    </div>
    @endisset
</div>
@endsection
