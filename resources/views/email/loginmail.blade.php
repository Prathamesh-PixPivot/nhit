@extends('email.layout.app')
@section('content')
    <td class="wrapper">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <p>Dear {{ $data['approver_name'] ?? '' }},</p>
                    <p> {{ $data['maker'] ?? '' }}</b>.</p>
                    <p>{{ $data['end'] ?? '' }}</p>
                    <p>Updated by: <b>{{ $data['updated_by'] ?? '' }}</b></p>
                    @if (!empty($data['rejection']))
                        <p>Rejection Remarks: <b>{{ $data['rejection'] ?? '' }}</b></p>
                    @endif

                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                        <tbody>
                            <tr>
                                <td align="left">
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <a href="{{ config('app.email_link') }}" target="_blank">Log
                                                        In</a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <h3>NHIT.</h3>
                </td>
            </tr>
        </table>
    </td>
@endsection
