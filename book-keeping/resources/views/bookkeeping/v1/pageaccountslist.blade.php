@extends('bookkeeping.v1.base')

@section('pagetitle', 'Accounts List')

@section('content')
<div id="accounts">
    <table>
        <tr>
            <td class="main">
                <table>
                    <tr>
                        <th class="inmain">コード</th>
                        <th class="inmain">大カテゴリ</th>
                        <th class="inmain">カテゴリ</th>
                        <th class="inmain">科目名</th>
                        <th class="inmain">説明</th>
                    </tr>
                    @foreach ($accounts_list as $accountKey => $account)
                    <tr class="{{ $account['trclass'] }}">
                        <td class="inmain">{{ $account['code'] }}</td>
                        <td class="inmain">{{ $account['type'] }}</td>
                        <td class="inmain">{{ $account['group_title'] }}</td>
                        <td class="inmain">{{ $account['title'] }}</td>
                        <td class="inmain">{{ $account['description'] }}</td>
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>
</div>
@endsection
