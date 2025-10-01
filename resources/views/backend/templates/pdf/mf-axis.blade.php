@if (!empty($data) && $data->isNotEmpty())
    <html>

    <head></head>

    <body>
        <table style="border: 1px solid #ddd;">
            <tr>
                <td style="border: 1px solid #ddd; text-align:center;" colspan="3">
                    <p>
                        <strong>
                            NHIT Western projects private limited<br>
                            Formely known as NATIONAL HIGHWAYS INFRA PROJECTS PRIVATE LIMITED<br>
                            Registered Office: G-5 6 Sector 10, Dwarka, New Delhi3- 110075, Phone: 011-25076536, FAX: 25076536<br>
                            CIN: U45201DL2020PTC366737
                        </strong>
                    </p>
                </td>
            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="3">To</td>
                <td style="*border: 1px solid #ddd;" colspan="2">Date: 21-03-2023</td>

            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="5">Axis Asset Management Company
                    Limited</td>
            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="5">New Delhi </td>
            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="5">Dear Sir/Mam,</td>
            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="5">Sub : Purchase of Units of Axis
                    Overnight Mutual Funds</td>
            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="5">With reference to the above
                    matter
                    we would like to purchase units for <strong>Rs. 0.00</strong> No Rupees Only
                    under
                    Axis Mutual Fund Collection A/C against the following folio numbers:- </td>

            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="1">S. No.</td>
                <td style="border: 1px solid #ddd;" colspan="1">From Investment Account Number
                </td>
                <td style="border: 1px solid #ddd;" colspan="1">From Project Name</td>
                <td style="border: 1px solid #ddd;" colspan="1">Folio Number</td>
                <td style="border: 1px solid #ddd;" colspan="1">Amount</td>

            </tr>
            @php
                $i = 1;
                $total = 0;
            @endphp
            @foreach ($data as $row)
                @php
                    $total += $row->amount;
                @endphp
                <tr>
                    <td style="border: 1px solid #ddd;" colspan="1">{{$i}}</td>
                    <td style="border: 1px solid #ddd;" colspan="1">{{ $row->account_full_name ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ddd;" colspan="1">{{ $row->project ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ddd;" colspan="1">{{ $row->folio ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ddd;" colspan="1">{{ $row->amount ?? 'N/A' }}</td>

                </tr>
                @php
                    $i++;
                @endphp
            @endforeach
            <tr>
                <td style="border: 1px solid #ddd;" colspan="4">Total</td>
                <td style="border: 1px solid #ddd;" colspan="1"> <strong>{{ $total ?? '0.00' }}</strong>

            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="5">We are effecting the fund
                    transfer
                    for the above in following account:-</td>

            </tr>
            <tr>

                <td style="border: 1px solid #ddd;" colspan="1">Beneficiary Name</td>
                <td style="border: 1px solid #ddd;" colspan="4">Axis Mutual Fund Collection A/C
                </td>

            </tr>
            <tr>

                <td style="border: 1px solid #ddd;" colspan="1">Beneficiary A/c Number</td>
                <td style="border: 1px solid #ddd;" colspan="4">31624650950</td>

            </tr>
            <tr>

                <td style="border: 1px solid #ddd;" colspan="1">IFSC Code</td>
                <td style="border: 1px solid #ddd;" colspan="4">SBIN0011777</td>

            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="5"> Kindly effect the mutual fund
                    transaction.</td>

            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="5">Thanking you,</td>
            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="5">Yours Sincerely,</td>
            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="5">For National Highways Infra
                    Projects Private Limited</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="2">Auth. Signatory <br><br><br>
                </td>
                <td style="border: 1px solid #ddd;" colspan="3">Auth. Signatory <br><br><br>
                </td>

            </tr>
        </table>


    </body>

    </html>
@else
    <p style="text-align: center;">No data found</p>
@endif
