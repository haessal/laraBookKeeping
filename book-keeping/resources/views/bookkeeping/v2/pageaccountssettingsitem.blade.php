@extends('bookkeeping.v2.baseaccountssettings')

@section('v2_page_accounts_settings_content')
<div class="h4 pt-3">{{{ __('Edit Account Item') }}}</div>
<hr>
<div class="container-fluid">
    @isset($accountsitem)
    <div class="row d-none d-md-block">
        <form method="POST" action="{{ route('v2_accounts_items', ['bookId' => $book['id'], 'accountsItemId' => $accountsitem['id']]) }}">
            @csrf
            <div class="form-group">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th class="table-active border border-secondary" width="25%">{{{ __('Type') }}}</th>
                            <td class="border border-secondary">{{{ $accountsitem['type'] }}}</td>
                        </tr>
                        <tr>
                            <th class="table-active border border-secondary" width="25%">{{{ __('Accounts Group') }}}</th>
                            <td class="border border-secondary">
                                <select name="accountgroup" class="form-control">
                                    @foreach ($accountsgroups as $accountGroupKey => $accountGroup)
                                    @if ($accountsitem['groupid'] == $accountGroupKey)
                                    <option value="{{ $accountGroupKey }}" selected>{{{ $accountGroup }}}</option>
                                    @else
                                    <option value="{{ $accountGroupKey }}">{{{ $accountGroup }}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th class="table-active border border-secondary" width="25%">{{{ __('Name') }}}</th>
                            <td class="border border-secondary"><input type="text" class="form-control" name="title" value="{{ $accountsitem['title'] }}"></td>
                        </tr>
                        <tr>
                            <th class="table-active border border-secondary" width="25%">{{{ __('Description') }}}</th>
                            <td class="border border-secondary">
                                <textarea class="form-control" name="description">{{{ $accountsitem['description'] }}}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <th class="table-active border border-secondary" width="25%">{{{ __('Attributes') }}}</th>
                            <td class="border border-secondary">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="attribute_selectable" id="md_selectable" value="1" {{{ $accountsitem['attribute_selectable'] }}}>
                                    <label class="form-check-label" for="md_selectable">{{{ __('Selectable') }}}</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th class="table-active border border-secondary" width="25%">{{{ __('Code for Previous version') }}}</th>
                            <td class="border border-secondary">{{{ $accountsitem['bk_code'] }}}</td>
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
        <form method="POST" action="{{ route('v2_accounts_items', ['bookId' => $book['id'], 'accountsItemId' => $accountsitem['id']]) }}">
            @csrf
            <div class="form-group">
                <table class="table table-bordered d-table d-md-none">
                    <tbody>
                        <tr><th class="table-active border border-secondary">{{{ __('Type') }}}</th></tr>
                        <tr><td class="border border-secondary">{{{ $accountsitem['type'] }}}</td></tr>
                        <tr><th class="table-active border border-secondary">{{{ __('Accounts Group') }}}</th></tr>
                        <tr>
                            <td class="border border-secondary">
                                <select name="accountgroup" class="form-control">
                                    @foreach ($accountsgroups as $accountGroupKey => $accountGroup)
                                    @if ($accountsitem['groupid'] == $accountGroupKey)
                                    <option value="{{ $accountGroupKey }}" selected>{{{ $accountGroup }}}</option>
                                    @else
                                    <option value="{{ $accountGroupKey }}">{{{ $accountGroup }}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr><th class="table-active border border-secondary">{{{ __('Name') }}}</th></tr>
                        <tr><td class="border border-secondary"><input type="text" class="form-control" name="title" value="{{ $accountsitem['title'] }}"></td></tr>
                        <tr><th class="table-active border border-secondary">{{{ __('Description') }}}</th></tr>
                        <tr>
                            <td class="border border-secondary">
                                <textarea class="form-control" name="description">{{{ $accountsitem['description'] }}}</textarea>
                            </td>
                        </tr>
                        <tr><th class="table-active border border-secondary">{{{ __('Attributes') }}}</th></tr>
                        <tr>
                            <td class="border border-secondary">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="attribute_selectable" id="selectable" value="1" {{{ $accountsitem['attribute_selectable'] }}}>
                                    <label class="form-check-label" for="selectable">{{{ __('Selectable') }}}</label>
                                </div>
                            </td>
                        </tr>
                        <tr><th class="table-active border border-secondary">{{{ __('Code for Previous version') }}}</th></tr>
                        <tr><td class="border border-secondary">{{{ $accountsitem['bk_code'] }}}</td></tr>
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
