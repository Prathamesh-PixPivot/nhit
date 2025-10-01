<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        @page {
            margin-top: 170px;
        }

        tr,
        td {
            *border: 1px solid #000;

        }

        header {
            position: fixed;
            top: -140px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        .footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
        }
    </style>
    <title></title>
</head>

<body>
    <header>
        <table width="100%" cellspacing="0" cellpadding="5">
            <tr>
                <td colspan="12" style="text-align: center; font-size: 18px; font-weight: 700; border: none;">
                    {{ config('app.full_name') }}
                </td>
            </tr>
            <tr>
                <td colspan="12" style="text-align: center; font-size: 16px; font-weight: 700; border: none;">Travel /
                    Expenses Reimbursement Form</td>
            </tr>
            <tr>
                <td colspan="12" style="font-size:14px;">
                    <hr>
                </td>
            </tr>

            <tr>
                <td colspan="3" style="font-size:14px;"><strong>Date:</strong>
                    {{ $note->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
                </td>
                <td colspan="3" style="font-size:14px;"><strong>Project Name:</strong>
                    {{ $note->project->project ?? '-' }}</td>
                <td colspan="3" style="font-size:14px;"><strong>Department:</strong>
                    {{ $note->selectUser ? $note->selectUser->department->name : $note->user->department->name ?? '-' }}
                </td>
                <td colspan="3" style="font-size:14px;"><strong>Note No.:</strong> {{ $note->note_no ?? '-' }}</td>
            </tr>
        </table>
    </header>
    <div class="footer">
        <script type="text/php">
            if (isset($pdf)) {
                $pdf->page_script('
                    $font = $fontMetrics->get_font("Arial, sans-serif", "normal");
                    $pdf->text(270, 820, "Page " . $PAGE_NUM . " of " . $PAGE_COUNT, $font, 12);
                ');
            }
        </script>
    </div>
    <table cellpadding="5" cellspacing="0" style=" table-layout: fixed; width: 100%;">
        {{-- <tr>
            <td colspan="12" style="text-align: center; font-size: 18px; font-weight: 700; border: none;">NHIT Western
                Projects Private Limited</td>
        </tr>
        <tr>
            <td colspan="12" style="text-align: center; font-size: 16px; font-weight: 700; border: none;">Travel /
                Expenses Reimbursement Form</td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"></td>
        </tr>
        <tr>
            <td colspan="3" style="font-size:14px;"><strong>Date:</strong>
                {{ $note->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
            </td>
            <td colspan="3" style="font-size:14px;"><strong>Project Name:</strong>
                {{ $note->project->project ?? '-' }}</td>
            <td colspan="3" style="font-size:14px;"><strong>Department:</strong>
                {{ $note->user->department->name ?? '-' }}</td>
            <td colspan="3" style="font-size:14px;"><strong>Note No.:</strong> {{ $note->note_no ?? '-' }}</td>
        </tr> --}}
        <tr>
            <td colspan="12" style="font-size:14px;"></td>
        </tr>
        <tr>
            <td colspan="4" style="font-size:14px;"></strong>Employee Name:</strong>
                {{ $note->selectUser ? $note->selectUser->name : $note->user->name ?? '-' }}
            </td>
            <td colspan="4" style="font-size:14px;"></strong>Employee ID:</strong>
                {{ $note->selectUser ? $note->selectUser->emp_id : $note->user->emp_id ?? '-' }}
            </td>
            <td colspan="4" style="font-size:14px;"></strong>Employee Designation:</strong>
                {{ $note->selectUser ? $note->selectUser->designation->name : $note->user->designation->name ?? '-' }}
            </td>

        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"></td>
        </tr>
        <tr>
            <td colspan="4" style="font-size:14px;"><strong>Date of Travel:</strong>
                {{ $note->date_of_travel ? date('d/m/Y', strtotime($note->date_of_travel)) : '-' }}</td>
            <td colspan="4" style="font-size:14px;"><strong>Mode of Travel:</strong>
                {{ $note->mode_of_travel ?? '-' }} </td>
            <td colspan="4" style="font-size:14px;"><strong>Travel Mode Eligibility:</strong>
                {{ $note->travel_mode_eligibility ?? '-' }}</td>

        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"></td>
        </tr>
        <tr>
            <td colspan="4" style="font-size:14px;"><strong>Initial Approver&#39;s Name:</strong>
                {{ $note->approver->name ?? '-' }}</td>
            <td colspan="4" style="font-size:14px;"><strong>Approver&#39;s designation:</strong>
                {{ $note->approver->designation->name ?? '-' }}</td>
            <td colspan="4" style="font-size:14px;"><strong>Approval Date:</strong>
                {{ $note->latestApproval?->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
            </td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"></td>
        </tr>
        <tr>
            <td colspan="2" style="font-size:14px;"><strong>Purpose of travel:</strong> </td>
            <td colspan="10" style="font-size:14px;">{{ $note->purpose_of_travel ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"></td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"><strong>Expense Details:</strong></td>
        </tr>
        <tr>
            <td colspan="2" style="border:1px solid;"><strong>Expense Type</strong></td>
            <td colspan="2" style="border:1px solid;"><strong>Bill Date</strong></td>
            <td colspan="2" style="border:1px solid;"><strong>Bill number</strong></td>
            <td colspan="2" style="border:1px solid;"><strong>Vendor Name</strong></td>
            <td colspan="1" style="border:1px solid;"><strong>Bill Amount</strong></td>
            <td colspan="1" style="border:1px solid;"><strong>Supporting Available</strong></td>
            <td colspan="2" style="border:1px solid;"><strong>Remarks (if any)</strong></td>
        </tr>
        @php
            $totalPayable = $note->expenses->sum('bill_amount');
            $advanceAdjusted = $note->adjusted;
            $netPayable = $totalPayable - $advanceAdjusted;
        @endphp
        @foreach ($note->expenses as $expense)
            <tr>
                <td colspan="2" style="border:1px solid;">{{ $expense->expense_type }}</td>
                <td colspan="2" style="border:1px solid;">
                    {{ $expense->bill_date ? date('d/m/Y', strtotime($expense->bill_date)) : '-' }}</td>
                <td colspan="2"
                    style="border:1px solid;           word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
            overflow-wrap: break-word;">
                    {{ $expense->bill_number }}</td>
                <td colspan="2" style="border:1px solid;">{{ $expense->vendor_name }}</td>
                <td colspan="1" style="border:1px solid; text-align: right;">{{ $expense->bill_amount }}</td>
                <td colspan="1" style="border:1px solid;">{{ $expense->supporting_available ? 'Yes' : 'No' }}</td>
                <td colspan="2" style="border:1px solid;">{{ $expense->remarks }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="7" style="border:1px solid;"><strong>Total Payable Amount</strong></td>
            <td colspan="5" style="border:1px solid;text-align: right;">
                <strong>{{ number_format($totalPayable, 2) }}</strong>
            </td>
        </tr>
        <tr>
            <td colspan="7" style="border:1px solid;"><strong>Advance Adjusted (if Any)</strong></td>
            <td colspan="5" style="border:1px solid;text-align: right;">
                <strong>{{ number_format($advanceAdjusted, 2) }}</strong>
            </td>
        </tr>
        <tr>
            <td colspan="7" style="border:1px solid;"><strong>Net Payable Amount</strong></td>
            <td colspan="5" style="border:1px solid;text-align: right;">
                <strong>{{ number_format($netPayable, 2) }}</strong>
            </td>
        </tr>

        <tr>
            <td colspan="12" style="font-size:14px;"></td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"><strong>Bank Details:</strong></td>
        </tr>
        <tr>
            <td colspan="3" style="font-size:14px;"><strong>Name of Account holder:</strong></td>
            <td colspan="9" style="font-size:14px;"> {{ $note->account_holder ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="3" style="font-size:14px;"><strong>Bank Name:</strong></td>
            <td colspan="9" style="font-size:14px;"> {{ $note->bank_name ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="3" style="font-size:14px;"><strong>Bank Account:</strong></td>
            <td colspan="9" style="font-size:14px;"> {{ $note->bank_account ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="3" style="font-size:14px;"><strong>IFSC:</strong></td>
            <td colspan="9" style="font-size:14px;"> {{ $note->IFSC_code ?? '-' }}</td>

        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"></td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;" style="font-size:14px;"><strong>Approval Matrix</strong></td>
        </tr>
        <tr>
            <td colspan="4" style="font-size:14px;"><strong>Prepared By:</strong>
                <div class="border p-4 mb-4 w-75">
                    @if ($note->user->file)
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
                    <strong>Name:</strong> {{ $note->user->name ?? '-' }} <br>
                    <strong>Designation:</strong> {{ $note->user->designation->name ?? '-' }} <br>
                    <strong>Date:</strong>
                    {{ $note->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }} <br>
                </div>
            </td>
            <td colspan="4" style="font-size:14px;"><strong>Approved By:</strong>
                @if ($note->latestApproval?->reviewer && $note->latestApproval?->status == 'A')

                    <div class="border p-4 mb-4 w-75">
                        @if ($note->latestApproval?->reviewer->file)
                            {{-- <img src="{{ asset('uploads/' . $note->latestApproval?->reviewer->file) }}" alt="logo"
                                width="130px" height="80px"> <br> --}}
                            @php
                                $filePath = public_path('uploads/' . $note->latestApproval?->reviewer->file);
                                $imageSrc = file_exists($filePath)
                                    ? asset('uploads/' . $note->latestApproval?->reviewer->file)
                                    : asset('uploads/test.png');
                            @endphp
                            <img src="{{ $imageSrc }}" alt="logo" width="100px" height="auto"> <br>
                        @endif
                        <strong>Name:</strong> {{ $note->latestApproval?->reviewer->name ?? '-' }} <br>
                        <strong>Designation:</strong> {{ $note->latestApproval?->reviewer->designation->name ?? '-' }}
                        <br>
                        <strong>Date:</strong>
                        {{ $note->latestApproval?->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
                        <br>
                    </div>
                @endif

            </td>
            <td colspan="4" style="font-size:14px;"><strong>Approval Date:</strong>
                {{ $note->latestApproval?->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
            </td>
        </tr>
        <tr>
            <td colspan="4" style="font-size:14px;"></td>
            <td colspan="4" style="font-size:14px;"></td>
            <td colspan="4" style="font-size:14px;"></td>

        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;">(Please attach supporting for above expenses along with this
                note)</td>
        </tr>
        <tr>
            <td colspan="12"></td>
        </tr>
    </table>
</body>

</html>
