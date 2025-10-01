@extends('email.layout.app')
@section('content')
    <td class="wrapper">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <p>Changes made in the Roles & Rules of NHIT Payment Software</p>
                    <p><strong>{{ $data['updated_by'] }}</strong> has made changes in the Roles/Rules of the NHIT Payment
                        Software.</p>
                    <p>To review the changes kindly login to the panel.</p>


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
