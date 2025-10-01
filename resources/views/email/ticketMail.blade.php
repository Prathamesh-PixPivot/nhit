@extends('email.layout.app')
@section('content')
    <td class="wrapper">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <h2>Dear {{ $data['name'] }},</h2>

                    <p>Your support ticket has been successfully created.</p>

                    <ul>
                        <li><strong>Ticket ID:</strong> {{ $data['ticket_id'] }}</li>
                        <li><strong>Priority:</strong> {{ $data['priority'] }}</li>
                        <li><strong>Entity:</strong> {{ $data['short_name'] }}</li>
                        <li><strong>Status:</strong> {{ $data['status'] }}</li>
                        <li><strong>Error:</strong> {{ $data['error'] }}</li>
                    </ul>

                    <p>Our team will get back to you shortly. If you have more details or updates, please reply to this
                        ticket.</p>


                    <p>Best regards,</p>
                    <p>Admin</p>
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
