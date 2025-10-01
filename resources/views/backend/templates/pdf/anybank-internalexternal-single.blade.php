@if (!empty($data) && $data->isNotEmpty())
    <html>

    <head></head>

    <body>
        <table style="border: 1px solid #000; width: 100%;" cellspacing="0">
            <tr>
                <td style="border: 0px solid #000;font-size: 10px;" colspan="1">
                    <strong>{{ config('app.note_icon') }}{{ \Carbon\Carbon::now()->format('y') }}
                        {{ $data[0]?->sl_no ?? '' }}</strong>
                </td>
                <td style="border: 1px solid #000;" colspan="1"><strong>Application for RTGS/NEFT Remittance</strong>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;" colspan="1"><strong>Our Account No.</strong></td>
                <td style="border: 1px solid #000;" colspan="1"><strong>{{ $data[0]->full_account_number ?? '' }} -
                        {{ $data[0]->account_full_name ?? '' }}
                    </strong></td>
            </tr>
            <tr>
                @php
                    $amt = $data[0]->amount ? round($data[0]->amount) : '';
                    $obj = new App\Helpers\IndianCurrency($amt);
                @endphp
                <td style="border: 1px solid #000;" colspan="2">
                    Please remit a sum of Rs. {{ $amt }}/- ({{ $obj->get_words() }}) from our above mentioned
                    account as detailed below:
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;" colspan="1"><strong> </strong></td>
                <td style="border: 1px solid #000;" colspan="1"><strong>Date:</strong></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;" colspan="1"><strong>Beneficiary Name</strong></td>
                <td style="border: 1px solid #000; font-size: 20px;" colspan="1">
                    <strong>{{ $data[0]->name_of_beneficiary ?? '' }}</strong>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;" colspan="1"><strong>Beneficiary Account No.</strong></td>
                <td style="border: 1px solid #000; font-size: 20px;" colspan="1">
                    <strong>{{ $data[0]->account_number ?? '' }}</strong>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;" colspan="1"><strong>LEI Number</strong></td>
                <td style="border: 1px solid #000;" colspan="1"><strong></strong></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;" colspan="1"><strong>Beneficiary Bank & Branch</strong></td>
                <td style="border: 1px solid #000;" colspan="1">
                    {{ $data[0]->name_of_bank ?? '' }}
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;" colspan="1"><strong>Remitter's LEI Number</strong></td>
                <td style="border: 1px solid #000;" colspan="1">984500A11F6BBB1EC010</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;" colspan="1"><strong>IFS Code</strong></td>
                <td style="border: 1px solid #000; font-size: 20px;" colspan="1">
                    <strong>{{ $data[0]->ifsc_code ?? '' }}</strong>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;" colspan="1"><strong>Amount to be remitted</strong></td>
                <td style="border: 1px solid #000;font-size: 25px; text-align:right;" colspan="1">
                    <strong>{{ $data[0]->amount ? round($data[0]->amount) : '' }}/-</strong>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;" colspan="1"><strong>Reference/Details, if any</strong></td>
                <td style="border: 1px solid #000;" colspan="1"><strong>Nil</strong></td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;" colspan="1"><strong>Purpose</strong></td>
                <td style="border: 1px solid #000;" colspan="1">{{ $data[0]->purpose ?? '' }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;" colspan="1"><strong>Charges</strong></td>
                <td style="border: 1px solid #000;" colspan="1">-</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;" colspan="1"><strong>Value Dating</strong></td>
                <td style="border: 1px solid #000;" colspan="1"> </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;" colspan="2">
                    <strong>CONDITIONS FOR TRANSFER</strong><br>
                    <p style=" font-size: 5px;">1. All payment instructions should be carefully checked by the remitter.
                        As crediting the proceeds of
                        the remittance is based on the beneficiary’s account number, the name of the other bank and its
                        branch
                        being correctly provided, SBI will not be responsible if these particulars are not provided
                        correctly by
                        the remitter.<br>2. Application/Message received after the business hours will be sent on the
                        immediate
                        next working day.<br>3. SBI shall not be responsible for any delay in the processing of the
                        payment due
                        to RBI RTGS system NOT being available/failure of internal communication system at the recipient
                        bank/branch/ incorrect information provided by the remitter/any incorrect credit accorded by the
                        recipient bank/branch due to incorrect information provided <br> by the
                        remitter.<br>4.(i)Remitting
                        branch
                        shall not be liable for any loss or damage arising or resulting from delay in transmission
                        delivery or
                        non-delivery of electronic message or any mistake, omission or error in transmission or delivery
                        thereof
                        or in encrypting/decrypting the message for any cause whatsoever or from the misinterpretation
                        when
                        received <br> or for the action of the destination bank or for any act beyond the control of
                        SBI.<br>(ii) If
                        the recipient branch is closed for any reason, the account shall be credited on the immediate
                        next
                        working day.<br>(iii) Bank is free to recover charges if any in respect of remittances returned
                        on
                        account of faulty/inadequate information.<br>5. We have fully read the terms and conditions of
                        the
                        RTGS/NEFT remittances and shall abide by the same.<br>6. The said payment is in accordance with
                        the
                        waterfall mechanisim as mentioned in Escrow Agreement.
                    </p>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;" colspan="1"><strong>Signature of the applicant(s)</strong></td>
                <td style="border: 1px solid #000;" colspan="1"><strong>For {{ config('app.full_name') }}</strong>
                </td>
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
                <td colspan="1" style="text-align: center; vertical-align: top; padding: 10px;">

                    @if ($approvers->count() > 0)
                        @php
                            $firstApprover = $approvers->first();
                        @endphp
                        @if ($firstApprover->status == 'A')
                            @if ($firstApprover->reviewer->file)
                                @php
                                    $filePathFirst = public_path('uploads/' . $firstApprover->reviewer->file);
                                    $imageSrcFirst = file_exists($filePathFirst)
                                        ? asset('uploads/' . $firstApprover->reviewer->file)
                                        : asset('uploads/test.png');
                                @endphp
                                <img src="{{ $imageSrcFirst }}" alt="logo" width="100px" height="auto"> <br>
                            @endif
                        @endif
                    @endif
                </td>
                <td colspan="1" style="text-align: center; vertical-align: top; padding: 10px;">

                    @if ($approvers->count() > 1)
                        @php
                            $secondApprover = $approvers[2] ?? null;
                        @endphp
                        @if ($secondApprover->status == 'A')
                            @if ($secondApprover->reviewer->file)
                                @php
                                    $filePathFirst = public_path('uploads/' . $secondApprover->reviewer->file);
                                    $imageSrcFirst = file_exists($filePathFirst)
                                        ? asset('uploads/' . $secondApprover->reviewer->file)
                                        : asset('uploads/test.png');
                                @endphp
                                <img src="{{ $imageSrcFirst }}" alt="logo" width="100px" height="auto"> <br>
                            @endif
                        @endif
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="1" style="text-align: center; vertical-align: top; padding: 10px;">
                    Authorized Signatory
                </td>
                <td colspan="1" style="text-align: center; vertical-align: top; padding: 10px;">
                    Authorized Signatory
                </td>
            </tr>
        </table>

    </body>

    </html>

@endif
