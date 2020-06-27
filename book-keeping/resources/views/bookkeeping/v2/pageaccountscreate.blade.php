@extends('bookkeeping.v2.baseaccounts')

@section('v2_page_accounts_content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10">
            <div class="container-fluid rounded border py-0">
                <div class="h4 pt-3">{{{ __('Add Account Group') }}}</div>
                <hr>
                <div class="container-fluid">
                    <div class="row d-none d-md-block"> 
                        <form method="POST" action="{{ route('v2_accounts_new', ['bookId' => $book['id']]) }}">
                            @csrf
                            <div class="form-group">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th class="table-active border border-secondary" width="25%">{{{ __('Type') }}}</th>
                                            <td class="border border-secondary">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="radio1" id="radio1a" checked>
                                                    <label class="form-check-label" for="radio1a">{{{ __('Assets') }}}&nbsp;&nbsp;&nbsp;</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="radio1" id="radio1b">
                                                    <label class="form-check-label" for="radio1b">{{{ __('Liabilities') }}}&nbsp;&nbsp;&nbsp;</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="radio1" id="radio1b">
                                                    <label class="form-check-label" for="radio1b">{{{ __('Expense') }}}&nbsp;&nbsp;&nbsp;</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="radio1" id="radio1b">
                                                    <label class="form-check-label" for="radio1b">{{{ __('Revenue') }}}&nbsp;&nbsp;&nbsp;</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="table-active border border-secondary" width="25%">{{{ __('Name') }}}</th>
                                            <td class="border border-secondary"><input type="text" class="form-control" name="title" value="title"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="text-right">
                                    <button name="update" value="update" type="submit" class="btn btn-success">{{{ __('Add') }}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row d-block d-md-none">
                        for mibile phone
                    </div>
                </div>
            </div>
            <div class="container-fluid rounded border mt-2">
                <div class="h4 pt-3">{{{ __('Add Account Item') }}}</div>
                <hr>
                <div class="container-fluid">
                    <div class="row d-none d-md-block">
                        <form method="POST" action="{{ route('v2_accounts_new', ['bookId' => $book['id']]) }}">
                            @csrf
                            <div class="form-group">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th class="table-active border border-secondary" width="25%">{{{ __('Accounts Group') }}}</th>
                                            <td class="border border-secondary">
                                                <select name="accountgroup" class="form-control">
                                                    <option value="1" selected>$type - $accountGroup</option>
                                                    <option value="2">$type - $accountGroup</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="table-active border border-secondary" width="25%">{{{ __('Name') }}}</th>
                                            <td class="border border-secondary"><input type="text" class="form-control" name="title" value="$title"></td>
                                        </tr>
                                        <tr>
                                            <th class="table-active border border-secondary" width="25%">{{{ __('Description') }}}</th>
                                            <td class="border border-secondary">
                                                <textarea class="form-control" name="description">$description</textarea>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="text-right">
                                    <button name="update" value="update" type="submit" class="btn btn-success">{{{ __('Add') }}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row d-block d-md-none">
                        for mibile phone
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
