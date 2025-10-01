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
                <td colspan="3">NHIPPL/Staff Salary</td>
                <td colspan="3">Date: 28-04-2023</td>

            </tr>
            <tr>
                <td colspan="6">The Senior Manager </td>
            </tr>
            <tr>
                <td colspan="6">State Bank of India</td>
            </tr>
            <tr>
                <td colspan="6">New Delhi-110001</td>
            </tr>
            <tr>
                <td colspan="6">Sub: Transfer of fund from our A/c No.
                    <strong>40797007409</strong>
                    BY RTGS/NEFT
                </td>
            </tr>
            <tr>
                <td colspan="6">Dear Sir,</td>
            </tr>
            <tr>
                <td colspan="6">Please transfer the Sum of Rs. <strong>0.00</strong> No Rupees
                    Only
                    from our Common Pool A/c No. <strong>40797007409</strong> to various accounts as
                    per
                    details below.</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="1">Sr No</td>
                <td style="border: 1px solid #ddd;" colspan="1">Name of the Bank</td>
                <td style="border: 1px solid #ddd;" colspan="1">Beneficiary Bank’s IFSC Code</td>
                <td style="border: 1px solid #ddd;" colspan="1">Account Number of Beneficiary
                </td>
                <td style="border: 1px solid #ddd;" colspan="1">Name of Beneficiary</td>
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
                    <td style="border: 1px solid #ddd;" colspan="1">{{ $row->name_of_bank ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ddd;" colspan="1">{{ $row->ifsc_code ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ddd;" colspan="1">{{ $row->account_number ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ddd;" colspan="1">{{ $row->name_of_beneficiary ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ddd;" colspan="1">{{ $row->amount ?? 'N/A' }}</td>
                </tr>
                @php
                    $i++;
                @endphp
            @endforeach
            <tr>
                <td style="border: 1px solid #ddd;" colspan="5"><strong> Total</strong></td>
                <td style="border: 1px solid #ddd;" colspan="1"> <strong>{{ $total ?? '0.00' }}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="6">“We undertake to indemnify SBI Bank for any losses that SBI Bank
                    may
                    incur by relying on these instructions”</td>
            </tr>
            <tr>
                <td colspan="6">Thanking you,</td>
            </tr>
            <tr>
                <td colspan="6">For National Highways Infra Projects Private Limited</td>
            </tr>
            <tr>
                <td style="border:1px solid #ddd;" colspan="3">Auth. Signatory<br><br><br></td>
                <td style="border:1px solid #ddd;" colspan="3">Auth. Signatory<br><br><br></td>
            </tr>
        </table>
    </body>

    </html>
@else
    <p style="text-align: center;">No data found</p>
@endif
