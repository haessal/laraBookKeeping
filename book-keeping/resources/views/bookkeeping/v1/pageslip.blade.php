@extends('bookkeeping.v1.base') @section('pagetitle', 'Slip') @section('content')
<div id="accounts">
    <table>
        <tr>
            <td class="top">
                <table>
                    <form method="POST" action="{{ route('v1_slip') }}">
                        @csrf
                        <tr>
                            <td class="in-top">{{ __('Debit') }}</td>
                            <td class="in-top">{{ __('Client') }}</td>
                            <td class="in-top">{{ __('Outline') }}</td>
                            <td class="in-top">{{ __('Credit') }}</td>
                            <td class="in-top">{{ __('Amount') }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="in-top">
                                <select name="debit">
                                    <option value="0"></option>
                                    @foreach ($account_title_list as $account_key => $account_title) @if ($add['debit']
                                    == $account_key)
                                    <option value="{{ $account_key }}" selected>{{ $account_title }}</option>
                                    @else
                                    <option value="{{ $account_key }}">{{ $account_title }}</option>
                                    @endif @endforeach
                                </select>
                            </td>
                            <td class="in-top">
                                <input value="{{ $add['client'] }}" size="15" name="client" type="text" />
                            </td>
                            <td class="in-top">
                                <input value="{{ $add['outline'] }}" size="40" name="outline" type="text" />
                            </td>
                            <td class="in-top">
                                <select name="credit">
                                    <option value="0"></option>
                                    @foreach ($account_title_list as $account_key => $account_title) @if ($add['credit']
                                    == $account_key)
                                    <option value="{{ $account_key }}" selected>{{ $account_title }}</option>
                                    @else
                                    <option value="{{ $account_key }}">{{ $account_title }}</option>
                                    @endif @endforeach
                                </select>
                            </td>
                            <td class="in-top">
                                <input value="{{ $add['amount'] }}" size="15" name="amount" type="text" />
                            </td>
                            <td><input name="buttons[add]" value="{{ __('Add') }}" type="submit" /></td>
                        </tr>
                    </form>
                </table>
            </td>
        </tr>
        <tr>
            <td><!-- This line shows white separater --></td>
        </tr>
        <tr>
            <td class="top">
                <!-- Multiple Add is not supported already -->
            </td>
        </tr>
        <tr>
            <td class="main">
                @empty($draftslip) {{ __('There is no item.') }} @else
                <form method="POST" action="{{ route('v1_slip') }}">
                    @csrf
                    <table>
                        <tr>
                            <td colspan="7">
                                Date:
                                <input value="{{ $slipdate }}" size="14" name="date" type="text" />
                            </td>
                        </tr>
                        <tr>
                            <th class="in-main">{{ __('No.') }}</th>
                            <th class="in-main">{{ __('Debit') }}</th>
                            <th class="in-main">{{ __('Client') }}</th>
                            <th class="in-main">{{ __('Outline') }}</th>
                            <th class="in-main">{{ __('Credit') }}</th>
                            <th class="in-main">{{ __('Amount') }}</th>
                            <th class="in-main"></th>
                        </tr>
                        @foreach ($draftslip as $draftslipKey => $draftslipItem)
                        <tr class="{{ $draftslipItem['evenOdd'] }}">
                            <td class="in-main" style="font-family: Consolas, 'Courier New', Courier, Monaco, monospace">
                                {{ $draftslipItem['no'] }}
                            </td>
                            <td class="in-main">{{ $draftslipItem['debit'] }}</td>
                            <td class="in-main">{{ $draftslipItem['client'] }}</td>
                            <td class="in-main">{{ $draftslipItem['outline'] }}</td>
                            <td class="in-main">{{ $draftslipItem['credit'] }}</td>
                            <td class="in-main" align="right">{{ $draftslipItem['amount'] }}</td>
                            <td class="in-main">
                                <input type="radio" name="modify_no" value="{{ $draftslipKey }}" />
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td class="in-main" colspan="5" align="right">{{ __('Total') }}</td>
                            <td class="in-main" align="right">{{ $totalamount }}</td>
                            <td><input name="buttons[delete]" value="{{ __('Delete') }}" type="submit" /></td>
                        </tr>
                    </table>
                    <input name="buttons[submit]" value="OK" type="submit" />
                </form>
                @endempty
            </td>
        </tr>
    </table>
</div>
@endsection
