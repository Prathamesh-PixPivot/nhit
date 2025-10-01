@if (!empty($data) && $data->isNotEmpty())
    <html>

    <head></head>

    <body>
        <table style="border: 1px solid #ddd;">
            <tr>
                <td style="border: 1px solid #ddd; text-align:center;" colspan="3">
                    <p>
                        <strong>
                            {{ config('app.full_name') }}<br>
                            Formely known as NATIONAL HIGHWAYS INFRA PROJECTS PRIVATE LIMITED<br>
                            Registered Office: G-5 6 Sector 10, Dwarka, New Delhi3- 110075, Phone: 011-25076536, FAX:
                            25076536<br>
                            CIN: U45201DL2020PTC366737
                        </strong>
                    </p>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="3"><strong>Application for RTGS/NEFT Remittance</strong>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>Debit Account Name</strong></td>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>Debit Account Number</strong></td>
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
                    <td style="border: 1px solid #ddd;" colspan="1">{{ $row->to ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ddd;" colspan="1">{{ $row->account_number ?? 'N/A' }}</td>
                    <td style="border: 1px solid #ddd;" colspan="1">{{ $row->amount ?? 'N/A' }}</td>
                </tr>
                @php
                    $i++;
                @endphp
            @endforeach
            <tr>
                <td style="border: 1px solid #ddd;" colspan="2"><strong>Total </strong></td>
                <td style="border: 1px solid #ddd;" colspan="1"> <strong>{{ $total ?? '0.00' }}</strong>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="3">Please remit a sum of Rs. <strong>0.00</strong> ( No
                    Rupees Only) from our above-mentioned account as detailed below:</td>
            </tr>
            <tr>
                <td style="*border: 1px solid #ddd;" colspan="1"><strong>Date:</strong></td>
                <td style="*border: 1px solid #ddd;" colspan="1">
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>Beneficiary Name</strong></td>
                <td style="border: 1px solid #ddd;" colspan="2">#N/A</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>Beneficiary Account No.</strong></td>
                <td style="border: 1px solid #ddd;" colspan="2">#N/A</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>LEI Number</strong></td>
                <td style="border: 1px solid #ddd;" colspan="2"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>Beneficiary Bank &amp; Branch</strong></td>
                <td style="border: 1px solid #ddd;" colspan="2">#N/A</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>Remitter’s LEI number</strong></td>
                <td style="border: 1px solid #ddd;" colspan="2"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>IFS Code</strong></td>
                <td style="border: 1px solid #ddd;" colspan="2">#N/A</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>Amount to be remitted</strong></td>
                <td style="border: 1px solid #ddd;" colspan="2"> - </td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>Reference/Details, if any</strong></td>
                <td style="border: 1px solid #ddd;" colspan="2">Nil</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>Purpose</strong></td>
                <td style="border: 1px solid #ddd;" colspan="2">#N/A</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>Charges</strong></td>
                <td style="border: 1px solid #ddd;" colspan="2">-</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>Value Dating</strong></td>
                <td style="border: 1px solid #ddd;" colspan="2"></td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="3"><strong>CONDITIONS FOR TRANSFER</strong></td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="3">1. All payment instructions should be carefully
                    checked by the remitter. As crediting the proceeds of the remittance is based on the beneficiary’s
                    account number, the name of the other bank and its branch being correctly provided, SBI will not be
                    responsible if these particulars are not provided correctly by the remitter.<br>2.
                    Application/Message received after the business hours will be sent on the immediate next working
                    day.<br>3. SBI shall not be responsible for any delay in the processing of the payment due to RBI
                    RTGS system NOT being available/failure of internal communication system at the recipient
                    bank/branch/ incorrect information provided by the remitter/any incorrect credit accorded by the
                    recipient bank/branch due to incorrect information provided by the remitter.<br>4.(i)Remitting
                    branch shall not be liable for any loss or damage arising or resulting from delay in transmission
                    delivery or non-delivery of electronic message or any mistake, omission or error in transmission or
                    delivery thereof or in encrypting/decrypting the message for any cause whatsoever or from the
                    misinterpretation when received or for the action of the destination bank or for any act beyond the
                    control of SBI.<br>(ii) If the recipient branch is closed for any reason, the account shall be
                    credited on the immediate next working day.<br>(iii) Bank is free to recover charges if any in
                    respect of remittances returned on account of faulty/inadequate information.<br>5. We have fully
                    read the terms and conditions of the RTGS/NEFT remittances and shall abide by the same.<br>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd;" colspan="1"><strong>Signature of the applicant(s)</strong></td>
                <td style="border: 1px solid #ddd;" colspan="2"><strong>For National Highways Infra Projects Private
                        Limited</strong></td>
            </tr>
            <tr>
                @php
                    $sl_no = $data[0]?->sl_no;

                    $approvers = \App\Models\BankLetterApprovalLog::with('reviewer')
                        ->where('sl_no', $sl_no)
                        ->where('status', 'A')
                        ->get()
                        ->filter(function ($log) {
                            return $log->reviewer->getRoleNames()->contains('PN Approver');
                        });
                    $nextApprovers = \App\Models\BankLetterApprovalLog::where('sl_no', $sl_no)
                        ->where('status', 'A')
                        ->get();
                    // dd($nextApprovers->last());

                    $approverNames = $approvers->map(function ($log) {
                        return $log->reviewer;
                    });
                @endphp
                <td colspan="2" style="text-align: center; vertical-align: top; padding: 10px;">

                    @if ($approvers->count() > 0)
                        @php
                            $firstApprover = $approvers->first();
                        @endphp
                        @if ($firstApprover->status == 'A')
                            <div style="height: 130px; display: flex; justify-content: center; align-items: center;">

                                @if ($firstApprover->reviewer->file)
                                    @php
                                        $filePathFirst = public_path('uploads/' . $firstApprover->reviewer->file);
                                        $imageSrcFirst = file_exists($filePathFirst)
                                            ? asset('uploads/' . $firstApprover->reviewer->file)
                                            : asset('uploads/test.png');
                                    @endphp
                                    <img src="{{ $imageSrcFirst }}" alt="logo"
                                        style="width: 100px; height: 100px; object-fit: contain;"> <br>
                                @endif
                            </div>
                        @endif
                    @endif
                    Authorized Signatory
                </td>
                <td colspan="2" style="text-align: center; vertical-align: top; padding: 10px;">

                    @if ($approvers->count() > 1)
                        @php
                            $secondApprover = $approvers[2] ?? null;
                        @endphp
                        @if ($secondApprover->status == 'A')
                            <div style="height: 130px; display: flex; justify-content: center; align-items: center;">

                                @if ($secondApprover->reviewer->file)
                                    @php
                                        $filePathFirst = public_path('uploads/' . $secondApprover->reviewer->file);
                                        $imageSrcFirst = file_exists($filePathFirst)
                                            ? asset('uploads/' . $secondApprover->reviewer->file)
                                            : asset('uploads/test.png');
                                    @endphp
                                    <img src="{{ $imageSrcFirst }}" alt="logo"
                                        style="width: 100px; height: 100px; object-fit: contain;"> <br>
                                @endif
                            </div>
                        @endif
                    @endif
                    Authorized Signatory
                </td>
            </tr>
        </table>
    </body>

    </html>
@else
    <p style="text-align: center;">No data found</p>
@endif
