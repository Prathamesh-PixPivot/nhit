@extends('email.layout.app')
@section('content')
    <td class="wrapper">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <p>Dear {{ $data['name'] ?? '' }},</p>

                    <p>Your account has been created successfully. Below are your login credentials:</p>

                    <p><strong>Email:</strong> {{ $data['email'] ?? '' }}</p>
                    <li><strong>Status:</strong> {{ $data['active'] }}</li>

                    {{-- <p><strong>Password:</strong> {{ $data['password'] ?? '' }}</p> --}}
                    @if (!empty($data['password']))
                        <li><strong>Password:</strong> {{ $data['password'] }}</li>
                    @endif

                    <p>Please change your password after logging in.</p>

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
