@extends('email.layout.app')
@section('content')
    <td class="wrapper">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <h2 style="color: #333;">Dear {{ $data['name'] ?? '' }},</h2>
                    <p style="color: #555;">You have received a new comment on a {{ $data['on'] ?? '-' }}.</p>

                    <hr>
                    <p>{!! $data['project'] ?? '-' !!}</p>
                    <p>{!! $data['short_name'] ?? '-' !!}</p>
                    <p>{!! $data['invoice_value'] ?? '-' !!}</p>
                    <p>{!! $data['name_of_supplier'] ?? '-' !!}</p>
                    <p>{!! $data['ticket_name'] ?? '-' !!}</p>
                    <p>{!! $data['comment_by'] ?? '-' !!}</p>
                    <p>{!! $data['comment_content'] ?? '-' !!}</p>
                    <hr>

                    <p style="color: #888;">This is an automated message. Please do not reply to this email.</p>
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                        <tbody>
                            <tr>
                                <td align="left">
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                            <tr>
                                                <td> <a href="{{ config('app.email_link') }}" target="_blank">Log
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
