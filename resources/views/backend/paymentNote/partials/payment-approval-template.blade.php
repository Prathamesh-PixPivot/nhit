<html>

<head>
    <title>Payments</title>
    <style type="text/css">
        tr,
        td {
            *border: 1px solid #000;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            *border: 1px solid black;
        }


        tfoot td {
            border-bottom: 1px solid black;
        }
    </style>

    </style>
</head>

<body>
    <table style="border: 0px solid #000; table-layout: fixed; width: 100%;" cellspacing="0">
        <tr>
            <td colspan="12" style="text-align:center; font-weight: 700; font-size:20px;">
                {{ config('app.full_name') }}
            </td>
        </tr>
        <tr>
            <td colspan="12" style="text-align:center; font-weight: 700; font-size:16px;">Note for Approval of Payment
            </td>
        </tr>
        <tr>
            <td colspan="6" style="font-size:14px;"><strong>Date:</strong>
                {{ $note->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
            </td>
            <td colspan="6" style="font-size:14px;"><strong>Note No.:</strong> {{ $note->note_no ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="4" style="font-size:14px;"><strong>Green Note No:</strong>
                @if ($note->greenNote)
                    {{ $note->greenNote->formatted_order_no ?? '-' }}
                @elseif ($note->reimbursementNote)
                    {{ $note->reimbursementNote->note_no }}
                @else
                    N/A
                @endif
            </td>
            <td colspan="4" style="font-size:14px;"><strong>Date:</strong>
                @if ($note->greenNote)
                    {{ optional($note->greenNote)->created_at ? date('d/m/Y', strtotime($note->greenNote->created_at)) : '-' }}
                @elseif ($note->reimbursementNote)
                    {{ $note->reimbursementNote->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
                @else
                    N/A
                @endif

            </td>
            <td colspan="4" style="font-size:14px;"><strong>Department:</strong>
                @if ($note->greenNote)
                    {{ $note->greenNote->department->name ?? '-' }}
                @elseif ($note->reimbursementNote)
                    {{ $note->reimbursementNote->selectUser ? $note->reimbursementNote->selectUser->department->name : $note->reimbursementNote->user->department->name ?? '' }}
                @else
                    N/A
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="6" style="font-size:14px;">
                <strong>Green Note App. Date:</strong>
                @if ($note->greenNote)
                    @php
                        $lastStep = optional(optional($note->greenNote)->approvalLogs)->last();
                        // dd($lastStep);
                    @endphp
                    @if ($lastStep)
                        {{ $lastStep->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') }}
                    @endif
                @elseif ($note->reimbursementNote)
                    {{ $note->reimbursementNote->latestApproval?->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
                @else
                    N/A
                @endif
            </td>
            <td colspan="6" style="font-size:14px;"><strong>Green Note Approver:</strong>

                @if ($note->greenNote)
                    @if ($lastStep)
                        {{ $lastStep->reviewer->name }}
                    @endif
                @elseif ($note->reimbursementNote)
                    {{ $note->reimbursementNote->approver->name ?? '-' }}
                @else
                    N/A
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"><strong>Subject:</strong> {{ $note->subject ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="6" style="font-size:14px;"><strong>Vendor Code:</strong>
                @if ($note->greenNote)
                    {{ $note->greenNote->supplier->vendor_code ?? '-' }}
                @elseif ($note->reimbursementNote)
                    N/A
                @else
                    N/A
                @endif
            </td>
            <td colspan="6" style="font-size:14px;"><strong>Vendor Name:</strong>
                @if ($note->greenNote)
                    {{ $note->greenNote->supplier->vendor_name ?? '-' }}
                @elseif ($note->reimbursementNote)
                    {{ $note->reimbursementNote->selectUser ? $note->reimbursementNote->selectUser->name : $note->reimbursementNote->user->name ?? '' }}
                @else
                    N/A
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="4" style="font-size:14px;"><strong>Invoice No.:</strong>
                @if ($note->greenNote)
                    {{ $note->greenNote->invoice_number ?? '-' }}
                @elseif ($note->reimbursementNote)
                    N/A
                @else
                    N/A
                @endif
            </td>
            <td colspan="4" style="font-size:14px;"><strong>Date:</strong>
                @if ($note->greenNote)
                    {{ optional($note->greenNote)->invoice_date ? date('d/m/Y', strtotime($note->greenNote->invoice_date)) : '-' }}
                @elseif ($note->reimbursementNote)
                    N/A
                @else
                    N/A
                @endif

            </td>
            <td colspan="4" style="font-size:14px;"><strong>Amount:</strong>
                @if ($note->greenNote)
                    {{ $note->greenNote->invoice_value ?? '-' }}
                @elseif ($note->reimbursementNote)
                    @php
                        $totalPayable = $note->reimbursementNote->expenses->sum('bill_amount') ?? 0;
                        $advanceAdjusted = $note->reimbursementNote->adjusted ?? 0;
                        $netPayableRn = $totalPayable - $advanceAdjusted;
                    @endphp
                    {{ \App\Helpers\Helper::formatIndianNumber($netPayableRn) }}
                @else
                    N/A
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"></td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"><strong>Invoice Approved by:</strong>
                @if ($note->greenNote)
                    @if ($lastStep)
                        {{ $lastStep->reviewer->name }}
                    @endif
                @elseif ($note->reimbursementNote)
                    {{ $note->reimbursementNote->approver->name ?? '-' }}
                @else
                    N/A
                @endif

            </td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"></td>
        </tr>
        <tr>
            <td colspan="6" style="font-size:14px;"><strong>LOA/PO No.:</strong>
                @if ($note->greenNote)
                    {{ $note->greenNote->order_no ?? '-' }}
                @elseif ($note->reimbursementNote)
                    N/A
                @else
                    N/A
                @endif
            </td>
            <td colspan="6" style="font-size:14px;"><strong>LOA/PO Date:</strong>
                @if ($note->greenNote)
                    {{ optional($note->greenNote)->order_date ? date('d/m/Y', strtotime($note->greenNote->order_date)) : '-' }}
                @elseif ($note->reimbursementNote)
                    N/A
                @else
                    N/A
                @endif
            </td>
        </tr>
        <tr>
            {{-- <td colspan="6"  style="font-size:14px;">Rate as per LOA/PO {{ $note->greenNote->total_amount ?? '-' }}</td> --}}
            <td colspan="12" style="font-size:14px;"><strong>LOA/PO Amount:</strong>
                @if ($note->greenNote)
                    {{ $note->greenNote->total_amount ?? '-' }}
                @elseif ($note->reimbursementNote)
                    N/A
                @else
                    N/A
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"></td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"><strong>Summary of payment</strong></td>
        </tr>
        <tr>
            <td colspan="4" style="border:1px solid;font-size:14px;"><strong>Particulars</strong></td>
            <!--<td colspan="4" style="border:1px solid;font-size:14px;"><strong>Remark</strong></td>-->
            <td colspan="3" style="border:1px solid;font-size:14px;"><strong>Payable Amount</strong></td>
            <td colspan="5" style="font-size:14px;"></td>

        </tr>
        <tr>
            <td colspan="4" style="border:1px solid;font-size:14px;"><strong>Taxable Amount</strong> </td>
            <td colspan="3" style="border:1px solid; text-align: right;font-size:14px;">
                @if ($note->greenNote)
                    {{ \App\Helpers\Helper::formatIndianNumber($note->greenNote->invoice_base_value) }}
                @elseif ($note->reimbursementNote)
                    {{ \App\Helpers\Helper::formatIndianNumber($netPayableRn) }}
                @else
                    N/A
                @endif
            </td>
            <td colspan="5" style="font-size:14px;"></td>

        </tr>
        <tr>
            <td colspan="4" style="border:1px solid;font-size:14px;"><strong>Add : GST</strong> </td>
            <!--<td colspan="4" style="border:1px solid;font-size:14px;"></td>-->
            <td colspan="3" style="border:1px solid;font-size:14px; text-align: right;">
                @if ($note->greenNote)
                    {{ \App\Helpers\Helper::formatIndianNumber($note->greenNote->invoice_gst) }}
                @else
                    N/A
                @endif
            </td>
            <td colspan="5" style="font-size:14px;"></td>
        </tr>
        <tr>
            <td colspan="4" style="border:1px solid;font-size:14px;"><strong>Add: Other charges</strong> </td>
            <!--<td colspan="4" style="border:1px solid;font-size:14px;"></td>-->
            <td colspan="3" style="border:1px solid;font-size:14px; text-align: right;">
                @if ($note->greenNote)
                    {{ \App\Helpers\Helper::formatIndianNumber($note->greenNote->invoice_other_charges) }}
                @else
                    N/A
                @endif
            </td>
            <td colspan="5" style="font-size:14px;"></td>
        </tr>
        <tr>
            <td colspan="4" style="border:1px solid;font-size:14px;"><strong>Gross Amount</strong></td>
            <!--<td colspan="4" style="border:1px solid;font-size:14px;"></td>-->
            <td colspan="3" style="border:1px solid;font-size:14px; text-align: right;">
                @if ($note->greenNote)
                    {{ \App\Helpers\Helper::formatIndianNumber($grossAmount) }}
                @elseif ($note->reimbursementNote)
                    {{ \App\Helpers\Helper::formatIndianNumber($netPayableRn) }}
                @else
                    N/A
                @endif
            </td>
            <td colspan="5" style="font-size:14px;"></td>
        </tr>



        @foreach ($lessParticulars as $particular)
            @if ($particular['particular'])
                <tr>
                    <td colspan="4" style="border:1px solid;font-size:14px;"><strong>Less:</strong>
                        {{ $particular['particular'] ?? '-' }}</td>
                    <!--<td colspan="4" style="border:1px solid;font-size:14px;"></td>-->
                    <td colspan="3" style="border:1px solid;font-size:14px; text-align: right;">
                        {{ \App\Helpers\Helper::formatIndianNumber($particular['amount']) }}
                    </td>
                    <td colspan="5" style="font-size:14px;"></td>
                </tr>
            @endif
        @endforeach
        @foreach ($addParticulars as $particular)
            @if ($particular['particular'])
                <tr>
                    <td colspan="4" style="border:1px solid;font-size:14px;"><strong>Add:</strong>
                        {{ $particular['particular'] ?? '-' }}</td>
                    <!--<td colspan="4" style="border:1px solid;font-size:14px;"></td>-->
                    <td colspan="3" style="border:1px solid;font-size:14px; text-align: right;">
                        {{ \App\Helpers\Helper::formatIndianNumber($particular['amount']) }}
                    </td>
                    <td colspan="5" style="font-size:14px;"></td>
                </tr>
            @endif
        @endforeach
        <tr>
            <td colspan="4" style="border:1px solid;font-size:14px;"><strong>Net Payable Amount</strong></td>
            <!--<td colspan="4" style="border:1px solid;font-size:14px;"></td>-->
            <td colspan="3" style="border:1px solid;font-size:14px; text-align: right;">
                {{ \App\Helpers\Helper::formatIndianNumber($netPayable) }}</td>
            <td colspan="5" style="font-size:14px;"></td>
        </tr>
        <tr>
            <td colspan="4" style="border:1px solid;font-size:14px;"><strong>Net Payable Amount (Round
                    Off)</strong></td>
            <!--<td colspan="4" style="border:1px solid;font-size:14px;"></td>-->
            <td colspan="3" style="border:1px solid;font-size:14px; text-align: right;">
                {{ \App\Helpers\Helper::formatIndianNumber($roundedNetPayable) }}</td>
            <td colspan="5" style="font-size:14px;"></td>
        </tr>
        <tr>
            <td colspan="3" style="font-size:14px;"><strong>Net Payable Amount (Words):</strong></td>
            <td colspan="9" style="font-size:14px;">{{ $netPayableWords }} only</td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"></td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"><strong>Bank Details:</strong></td>
        </tr>
        <tr>
            <td colspan="3" style="font-size:14px;"><strong>Name of Account holder</strong></td>
            <td colspan="9" style="font-size:14px;">
                @if ($note->greenNote)
                    {{ $note->greenNote->supplier->vendor_name ?? '-' }}
                @elseif ($note->reimbursementNote)
                    {{ $note->reimbursementNote->account_holder ?? '-' }}
                @else
                    N/A
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="3" style="font-size:14px;"><strong>Bank Name</strong></td>
            <td colspan="9" style="font-size:14px;">
                @if ($note->greenNote)
                    {{ $note->greenNote->supplier->name_of_bank ?? '-' }}
                @elseif ($note->reimbursementNote)
                    {{ $note->reimbursementNote->bank_name ?? '-' }}
                @else
                    N/A
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="3" style="font-size:14px;"><strong>Bank Account</strong></td>
            <td colspan="9" style="font-size:14px;">
                @if ($note->greenNote)
                    {{ $note->greenNote->supplier->account_number ?? '-' }}
                @elseif ($note->reimbursementNote)
                    {{ $note->reimbursementNote->bank_account ?? '-' }}
                @else
                    N/A
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="3" style="font-size:14px;"><strong>IFSC</strong></td>
            <td colspan="9" style="font-size:14px;">
                @if ($note->greenNote)
                    {{ $note->greenNote->supplier->ifsc_code ?? '-' }}
                @elseif ($note->reimbursementNote)
                    {{ $note->reimbursementNote->IFSC_code ?? '-' }}
                @else
                    N/A
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"></td>
        </tr>
        <tr>
            <td colspan="4" style="font-size:14px;"><strong>Recommendation of Payment</strong></td>
            <td colspan="8" style="font-size:14px;">{{ $note->recommendation_of_payment }}</td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"></td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"><strong>Approval Matrix</strong></td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td colspan="3" style="font-size:14px;"><strong>Name</strong></td>
            <td colspan="3" style="font-size:14px;"><strong>Designation</strong></td>
            <td colspan="3" style="font-size:14px;"><strong>Date &amp; Signature</strong></td>
        </tr>
        <tr>
            <td colspan="3" style="font-size:14px;">Maker</td>
            <td colspan="3" style="font-size:14px;">{{ $note->user->name ?? '-' }}</td>
            <td colspan="3" style="font-size:14px;">{{ $note->user->designation->name ?? '-' }}</td>
            <td colspan="3" style="font-size:14px;">
                @if (!empty(optional($note->user)->file))
                    {{-- <img src="{{ asset('uploads/' . $note->user->file) }}" alt="logo" width="130px"
                        height="80px"> <br> --}}
                    @php
                        $filePathUser = public_path('uploads/' . $note->user->file);
                        $imageSrcUser = file_exists($filePathUser)
                            ? asset('uploads/' . $note->user->file)
                            : asset('uploads/test.png');
                    @endphp
                    <img src="{{ $imageSrcUser }}" alt="logo" width="100px" height="auto">
                    <br>
                @endif
                {{ $note->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }} <br>

            </td>
        </tr>
        @php
            $approvers = $note->paymentApprovalLogs->filter(function ($step) {
                return $step->reviewer->getRoleNames()->contains('PN Approver') && $step->status == 'A';
            });
        @endphp

        <tr>
            @if ($approvers->count() > 0)
                <td colspan="3" style="font-size:14px;">Approver 1</td>
                @php
                    $firstApprover = $approvers->first();
                @endphp
                @if ($firstApprover->status == 'A')
                    <td colspan="3" style="font-size:14px;">
                        {{ $firstApprover->reviewer->name ?? '-' }}
                    </td>
                    <td colspan="3" style="font-size:14px;">
                        {{ $firstApprover->reviewer->designation->name ?? '-' }}</td>
                    <td colspan="3" style="font-size:14px;">
                        @if ($firstApprover->reviewer->file)
                            {{-- <img src="{{ asset('uploads/' . $firstApprover->reviewer->file) }}" alt="logo"
                                width="130px" height="80px"> <br> --}}
                            @php
                                $filePathFirst = public_path('uploads/' . $firstApprover->reviewer->file);
                                $imageSrcFirst = file_exists($filePathFirst)
                                    ? asset('uploads/' . $firstApprover->reviewer->file)
                                    : asset('uploads/test.png');
                            @endphp
                            <img src="{{ $imageSrcFirst }}" alt="logo" width="100px" height="auto"> <br>
                        @endif
                        {{ $firstApprover->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
                        <br>
                    </td>
                @endif
            @endif
        </tr>
        <tr>
            @if ($approvers->count() > 1)
                <td colspan="3" style="font-size:14px;">Approver 2</td>
                @php
                    $secondApprover = $approvers[2] ?? null;
                @endphp
                @if ($secondApprover->status == 'A')
                    <td colspan="3" style="font-size:14px;">
                        {{ $secondApprover->reviewer->name ?? '-' }}
                    </td>
                    <td colspan="3" style="font-size:14px;">
                        {{ $secondApprover->reviewer->designation->name ?? '-' }}</td>
                    <td colspan="3" style="font-size:14px;">
                        @if ($secondApprover->reviewer->file)
                            {{-- <img src="{{ asset('uploads/' . $secondApprover->reviewer->file) }}" alt="logo"
                                width="130px" height="80px"> <br> --}}
                            @php
                                $filePathSecond = public_path('uploads/' . $secondApprover->reviewer->file);
                                $imageSrcSecond = file_exists($filePathSecond)
                                    ? asset('uploads/' . $secondApprover->reviewer->file)
                                    : asset('uploads/test.png');
                            @endphp
                            <img src="{{ $imageSrcSecond }}" alt="logo" width="100px" height="auto"> <br>
                        @endif
                        {{ $secondApprover->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
                        <br>
                    </td>
                @endif

            @endif
        </tr>
        <tr>
            @if ($approvers->count() > 2)
                <td colspan="3" style="font-size:14px;">Approver 3</td>
                @php
                    $thirdApprover = $approvers[3] ?? null;
                @endphp
                @if ($thirdApprover->status == 'A')
                    <td colspan="3" style="font-size:14px;">
                        {{ $thirdApprover->reviewer->name ?? '-' }}
                    </td>
                    <td colspan="3" style="font-size:14px;">
                        {{ $thirdApprover->reviewer->designation->name ?? '-' }}</td>
                    <td colspan="3" style="font-size:14px;">
                        @if ($thirdApprover->reviewer->file)
                            {{-- <img src="{{ asset('uploads/' . $thirdApprover->reviewer->file) }}" alt="logo"
                                width="130px" height="80px"> <br> --}}
                            @php
                                $filePathThird = public_path('uploads/' . $thirdApprover->reviewer->file);
                                $imageSrcThird = file_exists($filePathThird)
                                    ? asset('uploads/' . $thirdApprover->reviewer->file)
                                    : asset('uploads/test.png');
                            @endphp
                            <img src="{{ $imageSrcThird }}" alt="logo" width="100px" height="auto"> <br>
                        @endif
                        {{ $thirdApprover->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
                        <br>
                    </td>
                @endif
            @endif
        </tr>
        <tr>
            @if ($approvers->count() > 3)
                <td colspan="3" style="font-size:14px;">Approver 4</td>
                @php
                    $fourApprover = $approvers[4] ?? null;
                @endphp
                @if ($fourApprover->status == 'A')
                    <td colspan="3" style="font-size:14px;">
                        {{ $fourApprover->reviewer->name ?? '-' }}
                    </td>
                    <td colspan="3" style="font-size:14px;">
                        {{ $fourApprover->reviewer->designation->name ?? '-' }}</td>
                    <td colspan="3" style="font-size:14px;">
                        @if ($fourApprover->reviewer->file)
                            {{-- <img src="{{ asset('uploads/' . $fourApprover->reviewer->file) }}" alt="logo"
                                width="130px" height="80px"> <br> --}}
                            @php
                                $filePathThird = public_path('uploads/' . $fourApprover->reviewer->file);
                                $imageSrcThird = file_exists($filePathThird)
                                    ? asset('uploads/' . $fourApprover->reviewer->file)
                                    : asset('uploads/test.png');
                            @endphp
                            <img src="{{ $imageSrcThird }}" alt="logo" width="100px" height="auto"> <br>
                        @endif
                        {{ $fourApprover->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
                        <br>
                    </td>
                @endif
            @endif
        </tr>
        <tr>
            <td colspan="12"> </td>
        </tr>
    </table>
</body>

</html>
