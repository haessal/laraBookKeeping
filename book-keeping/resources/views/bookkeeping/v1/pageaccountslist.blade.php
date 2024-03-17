@extends('bookkeeping.v1.base') @section('pagetitle', 'Accounts List') @section('content')
<div id="accounts">
    <table>
        <tr>
            <td class="main">
                <table>
                    <tr>
                        <th class="in-main">{{ __('Code') }}</th>
                        <th class="in-main">{{ __('Type') }}</th>
                        <th class="in-main">{{ __('Account Group Title') }}</th>
                        <th class="in-main">{{ __('Account Title') }}</th>
                        <th class="in-main">{{ __('Description') }}</th>
                    </tr>
                    @foreach ($accounts_list as $accountKey => $account)
                    <tr class="{{ $account['evenOdd'] }}">
                        <td class="in-main">{{ $account['code'] }}</td>
                        <td class="in-main">{{ $account['type'] }}</td>
                        <td class="in-main">{{ $account['group_title'] }}</td>
                        <td class="in-main">{{ $account['title'] }}</td>
                        <td class="in-main">{{ $account['description'] }}</td>
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>
</div>
@endsection
