@if (!empty($data) && $data->isNotEmpty())
    <html>

    <head>
    </head>

    <body>
        <table style="border: 1px solid #ddd;">
            <tr>
                <td style="border: 1px solid #ddd; text-align:center;" colspan="3">
                    <p>
                        <strong>
                            NHIT Western projects private limited<br>
                            Formely known as NATIONAL HIGHWAYS INFRA PROJECTS PRIVATE LIMITED<br>
                            Registered Office: G-5 6 Sector 10, Dwarka, New Delhi3- 110075, Phone: 011-25076536, FAX:
                            25076536<br>
                            CIN: U45201DL2020PTC366737
                        </strong>
                    </p>
                </td>
            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="5">{{ $groupRow->ref_no ?? 'N/A' }}
                    {{ $groupRow->sl_no ?? 'N/A' }}</td>
                <td style="*border: 1px solid #ddd;" colspan="2">Date: {{ \Carbon\Carbon::now()->toDateTimeString() }}
                </td>
            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="7">The Senior Manager </td>
            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="7">State Bank Of India</td>
            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="7">New Delhi-110001.</td>
            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="7"><strong>Sub: Transfer of
                        funds<strong></td>
            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="7">Dear Sir,</td>
            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="7">Please transfer the Sums as
                    mentioned below to the various accounts as per details given.</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>Sr No</strong></td>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>Name</strong></td>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>From Account Number</strong></td>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>To Account Name</strong></td>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>To Account Number</strong></td>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>Purpose</strong></td>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>Amount</strong></td>
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
                    <td style="border: 1px solid #ddd;" colspan="1">{{ $i }}</td>
                    <td style="border: 1px solid #ddd;" colspan="1">{{ $row->account_full_name ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ddd;" colspan="1">{{ $row->full_account_number ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ddd;" colspan="1">{{ $row->name_of_beneficiary ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ddd;" colspan="1">{{ $row->account_number ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ddd;" colspan="1">{{ $row->purpose ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ddd;" colspan="1">{{ $row->amount ?? 'N/A' }}
                    </td>
                </tr>
                @php
                    $i++;
                @endphp
            @endforeach
            <tr>
                <td style="border: 1px solid #ddd;" colspan="6"><strong>Total</strong></td>
                <td style="border: 1px solid #ddd;" colspan="1"> <strong>{{ $total ?? '0.00' }}</strong>
                </td>
            </tr>
            <tr>
                <td style="*border: 1px solid #ddd; font-size: 13px;" colspan="7">1. All
                    payment
                    instructions should be carefully checked by the remitter. As crediting the
                    proceeds
                    of the remittance is based on the beneficiary’s account number, the name of the
                    other bank and its branch being correctly provided, SBI will not be responsible
                    if
                    these particulars are not provided correctly by the remitter.<br>2.
                    Application/Message received after the business hours will be sent on the
                    immediate
                    next working day.<br>3. SBI shall not be responsible for any delay in the
                    processing
                    of the payment due to RBI RTGS system NOT being available/failure of internal
                    communication system at the recipient bank/branch/ incorrect information
                    provided by
                    the remitter/any incorrect credit accorded by the recipient bank/branch due to
                    incorrect information provided by the remitter.<br>4.(i)Remitting branch shall
                    not
                    be liable for any loss or damage arising or resulting from delay in transmission
                    delivery or non-delivery of electronic message or any mistake, omission or error
                    in
                    transmission or delivery thereof or in encrypting/decrypting the message for any
                    cause whatsoever or from the misinterpretation when received or for the action
                    of
                    the destination bank or for any act beyond the control of SBI.<br>(ii) If the
                    recipient branch is closed for any reason, the account shall be credited on the
                    immediate next working day.<br>(iii) Bank is free to recover charges if any in
                    respect of remittances returned on account of faulty/inadequate
                    information.<br>5.
                    We have fully read the terms and conditions of the RTGS/NEFT remittances and
                    shall
                    abide by the same.<br>6. The said payment is in accordance with the waterfall
                    mechanisim as mentioned in Escrow Agreement.</td>
            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="7">Thanking you,</td>
            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="7">For
                    {{ config('app.full_name') }}
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="3"><br><br><br><br>Auth. Signatory
                </td>
                <td style="border: 1px solid #ddd;" colspan="5"><br><br><br><br>Auth. Signatory
                </td>
            </tr>
        </table>
    </body>

    </html>
@else
    <p style="text-align: center;">No data found</p>
@endif
