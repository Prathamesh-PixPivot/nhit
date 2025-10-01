<html>

<head>
    <title>Expense Approval</title>
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
</head>

<body>
    <header>
        <table width="100%" cellspacing="0" cellpadding="5">
            <tr>
                <td colspan="12" style="text-align: center; font-size: 20px; font-weight: 700; border: none;">
                    {{ config('app.full_name') }}
                </td>
            </tr>
            <tr>
                <td colspan="12" style="text-align: center; font-size: 18px; font-weight: 700; border: none;">
                    Note for Approval of {{ $note->approval_for ?? '-' }} payment
                </td>
            </tr>
            <tr>
                <td colspan="12" style="border: none; font-size: 14px;">
                    <hr>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="border: none; font-size: 14px;">
                    <strong>Date:</strong>
                    {{ $note->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
                </td>
                <td colspan="3" style="border: none; font-size: 14px;">
                    <strong>Project:</strong> {{ $note->vendor->project ?? '-' }}
                </td>
                <td colspan="3" style="border: none; font-size: 14px;">
                    <strong>Department:</strong> {{ $note->department->name ?? '-' }}
                </td>
                <td colspan="3" style="border: none; font-size: 14px;">
                    <strong>Note No.:</strong> {{ $note->formatted_order_no ?? '-' }}
                </td>
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

    <table cellpadding="0" cellspacing="1" style="width: fixed; *width: inherit;">

        <tr>
            <td colspan="6" style="border:none;font-size:14px;"><strong>Purchase/Work Order No:</strong>
                {{ $note->order_no ?? '-' }}</td>
            <td colspan="6" style="border:none;font-size:14px;"><strong>Purchase/Work order Date:</strong>
                {{ $note->order_date ? date('d/m/Y', strtotime($note->order_date)) : '-' }}
            </td>
        </tr>
        <tr>
            <td colspan="3" style="border:none;font-size:14px;"><strong>Amount of PO/WO</strong></td>
            <td colspan="3" style="border:none;font-size:14px;"><strong>Taxable Value:</strong><br> Rs.
                {{ \App\Helpers\Helper::formatIndianNumber($note->base_value) ?? '-' }} <br><strong>GST:</strong><br> Rs
                {{ \App\Helpers\Helper::formatIndianNumber($note->gst) ?? '-' }}</td>
            <td colspan="3" style="border:none;font-size:14px;"><strong>Other Charges:</strong><br> Rs.
                {{ \App\Helpers\Helper::formatIndianNumber($note->other_charges) ?? '-' }}</td>
            <td colspan="3" style="border:none;font-size:14px;"><strong>Total PO/WO Value:</strong><br> Rs.
                {{ \App\Helpers\Helper::formatIndianNumber($note->total_amount) ?? 0 }}</td>
        </tr>
        <tr>
            <td colspan="12">
                <hr>
            </td>
        </tr>
        <tr>
            <td colspan="4" style="border:none;font-size:14px;"><strong>Name of Supplier:</strong>
                {{ $note->supplier->vendor_name ?? '-' }}</td>
            <td colspan="4" style="border:none;font-size:14px;"><strong>Vendor&#39;s MSME Classification:</strong>
                {{ $note->supplier->msme_classification ?? '-' }}</td>
            <td colspan="4" style="border:none;font-size:14px;"><strong>Activity Type:</strong>
                {{ $note->supplier->activity_type ?? '-' }}</td>
        </tr>
        <tr>

            <td colspan="4" style="border:none;font-size:14px;"><strong>Protest Note
                    Required:</strong> {{ $note->protest_note_raised == 'Y' ? 'Yes' : 'NO' }} </td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;">
                <hr>
            </td>
        </tr>
        <tr>
            <td colspan="12" style="border:none;font-size:14px;"><strong>Brief of Goods / Services:</strong>
                {{ $note->brief_of_goods_services ?? '-' }}
            </td>
            {{-- <td colspan="10" style="border:none;">(Text)</td> --}}
        </tr>
        <tr>
            <td colspan="2" style="border:none;font-size:14px;"><strong>Invoice No.:</strong>
                {{ $note->invoice_number ?? '-' }}</td>
            <td colspan="2" style="border:none;font-size:14px;"><strong>Invoice Date:</strong>
                {{ $note->invoice_date ? date('d/m/Y', strtotime($note->invoice_date)) : '-' }}
            </td>
            <td colspan="8" style="border:none;font-size:14px; ">
                <table style="width: 75%; border-collapse: collapse; margin-bottom: 10px;">
                    <tr>
                        <td style="text-align: left; padding: 5px;">
                            <strong>Taxable Value:</strong>
                        </td>
                        <td style="text-align: right; padding: 5px;">
                            {{ \App\Helpers\Helper::formatIndianNumber($note->invoice_base_value) ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left; padding: 5px;">
                            <strong>Add GST on above:</strong>
                        </td>
                        <td style="text-align: right; padding: 5px;">
                            {{ \App\Helpers\Helper::formatIndianNumber($note->invoice_gst) ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left; padding: 5px;">
                            <strong>Invoice Other Charges:</strong>
                        </td>
                        <td style="text-align: right; padding: 5px;">
                            {{ \App\Helpers\Helper::formatIndianNumber($note->invoice_other_charges) ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left; padding: 5px;">
                            <strong>Invoice Value:</strong>
                        </td>
                        <td style="text-align: right; padding: 5px;">
                            {{ \App\Helpers\Helper::formatIndianNumber($note->invoice_value) ?? '-' }}
                        </td>
                    </tr>

                </table>


                {{-- <div class="d-flex justify-content-between w-75">
                    <div style="text-align: left;"><strong>Add GST on above:</strong></div>
                    <div style="text-align: right;">
                        {{ \App\Helpers\Helper::formatIndianNumber($note->invoice_gst) ?? '-' }}</div>
                </div>
                <div class="d-flex justify-content-between w-75">
                    <div style="text-align: left;"><strong>Invoice Other Charges:</strong></div>
                    <div style="text-align: right;">
                        {{ \App\Helpers\Helper::formatIndianNumber($note->invoice_other_charges) ?? '-' }}</div>
                </div>
                <div class="d-flex justify-content-between w-75">
                    <div style="text-align: left;"><strong>Invoice Value:</strong></div>
                    <div style="text-align: right;">
                        {{ \App\Helpers\Helper::formatIndianNumber($note->invoice_value) ?? '-' }}</div>
                </div> --}}
            </td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;">
                <hr>
            </td>
        </tr>
        <tr>
            <td colspan="6" style="border:none;font-size:14px;"><strong>Contract Period:</strong> From
                {{ $note->contract_start_date ? date('d/m/Y', strtotime($note->contract_start_date)) : '-' }} To
                {{ $note->contract_end_date ? date('d/m/Y', strtotime($note->contract_end_date)) : '-' }}
            </td>
            <td colspan="6" style="border:none;font-size:14px;"><strong>Appointed date/Date of start of
                    supply:</strong>
                {{ $note->appointed_start_date ? date('d/m/Y', strtotime($note->appointed_start_date)) : '-' }}

            </td>
        </tr>
        <tr>
            <td colspan="12">
                <hr>
            </td>
        </tr>
        <tr>
            <td colspan="12" style="border:none;font-size:14px;"><strong>Period of Supply of services/goods
                    Invoiced:</strong> from
                {{ $note->supply_period_start ? date('d/m/Y', strtotime($note->supply_period_start)) : '-' }}
                to
                {{ $note->supply_period_end ? date('d/m/Y', strtotime($note->supply_period_end)) : '-' }}
            </td>
        </tr>
        <tr>
            <td colspan="12" style="border:none;font-size:14px;"><strong>Whether contract period completed:</strong>
                {{ $note->whether_contract == 'Y' ? 'Yes' : 'NO' }}
            </td>
        </tr>
        <tr>
            <td colspan="12" style="border:none;font-size:14px;"><strong>Extension of contract period
                    executed:</strong> {{ $note->extension_work_order == 'Y' ? 'Yes' : 'NO' }}</td>
        </tr>
        <tr>
            <td colspan="12" style="border:none;font-size:14px;"><strong>Delayed damages:</strong>
                {{ $note->delayed_damages ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;">
                <hr>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="font-size:14px;"><strong>Budget Utilisation:</strong></td>
            <td colspan="3" style="border:1px solid #000;font-size:14px;"><strong>Budget Expenditure Rs.</strong>
            </td>
            <td colspan="3" style="border:1px solid #000;font-size:14px;"><strong>Actual Expenditure Rs.</strong>
            </td>
            <td colspan="3" style="border:1px solid #000;font-size:14px;"><strong>Expenditure over budget</strong>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="font-size:14px;"> </td>
            <td colspan="3"style="font-size:14px; border:1px solid #000; text-align: right;">
                {{ \App\Helpers\Helper::formatIndianNumber($note->budget_expenditure) ?? '-' }} </td>
            <td colspan="3"style="font-size:14px; border:1px solid #000; text-align: right;">
                {{ \App\Helpers\Helper::formatIndianNumber($note->actual_expenditure) ?? '-' }}</td>
            <td colspan="3"style="font-size:14px; border:1px solid #000; text-align: right;">
                {{-- {{ \App\Helpers\Helper::formatIndianNumber($note->expenditure_over_budget) ?? '-' }} --}}
                @if ($note->expenditure_over_budget === 'NO')
                    NO
                @elseif(is_numeric($note->expenditure_over_budget))
                    {{ \App\Helpers\Helper::formatIndianNumber($note->expenditure_over_budget) }}
                @else
                    -
                @endif

            </td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;">
                <hr>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="font-size:14px;"><strong>Nature of Expenses</strong></td>
            <td colspan="9" style="font-size:14px;">{{ $note->nature_of_expenses ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="3" style="font-size:14px;"><strong>Milestone Status Achived ?</strong></td>
            <td colspan="9" style="font-size:14px;">{{ $note->milestone_status == 'Y' ? 'Yes' : 'NO' }}</td>
        </tr>
        @if ($note->milestone_status == 'Y')
            <tr>
                <td colspan="3" style="font-size:14px;"><strong>Milestone Remarks</strong></td>
                <td colspan="9" style="font-size:14px;">{{ $note->milestone_remarks == 'Y' ? 'Yes' : 'NO' }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="3" style="font-size:14px;"><strong>Expense amount within contract</strong></td>
            <td colspan="9" style="font-size:14px;">
                {{ $note->expense_amount_within_contract == 'Y' ? 'Yes' : 'NO' }}</td>
        </tr>
        <tr>
            <td colspan="3" style="font-size:14px;"><strong>Upload documents</strong></td>
            <td colspan="9" style="font-size:14px;">
                @if ($documents->isNotEmpty())
                    @foreach ($documents as $index => $document)
                        <a href="{{ asset('notes/documents/' . $document->file_path) }}" target="_blank">
                            {{ $document->name }}
                        </a> , <br>
                    @endforeach
                @else
                    No documents
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;">
                <hr>
            </td>
        </tr>
        <tr>
            <td colspan="4" style="font-size:14px;">
                <div class=" border p-4 mb-4">
                    <!-- Display the first approver's image -->
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
            <td colspan="4" style="font-size:14px;">
                @php
                    $approvers = $note->approvalLogs
                        ->filter(function ($step) {
                            return $step->reviewer->getRoleNames()->contains('GN Approver') && $step->status == 'A';
                        })
                        ->values();
                @endphp

                @if ($approvers->count() > 0)
                    <div class="approver-info border p-2 mb-4">
                        @php
                            $firstApprover = $approvers->first();
                        @endphp
                        @if ($firstApprover->status == 'A')
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
                            <strong>Name:</strong> {{ $firstApprover->reviewer->name ?? '-' }} <br>
                            <strong>Designation:</strong> {{ $firstApprover->reviewer->designation->name ?? '-' }} <br>
                            <strong>Date:</strong>
                            {{ $firstApprover->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
                            <br>
                        @endif
                    </div>
                @else
                    __________________
                @endif
            </td>
            <td colspan="4" style="font-size:14px;">
                @foreach ($note->approvalLogs as $index => $step)
                    @php
                        // Get logged-in user's roles
                        $userStepRoles = $step->reviewer->getRoleNames();
                    @endphp


                    @if ($userStepRoles->contains('Qs'))
                        <div class="approver-info border p-4 mb-4">
                            <!-- Display the first approver's image -->
                            @if ($step->reviewer->file)
                                {{-- <img src="{{ asset('uploads/' . $step->reviewer->file) }}" alt="logo"
                                    width="130px" height="80px"> <br> --}}
                                @php
                                    $filePathStep = public_path('uploads/' . $step->reviewer->file);
                                    $imageSrcStep = file_exists($filePathStep)
                                        ? asset('uploads/' . $step->reviewer->file)
                                        : asset('uploads/test.png');
                                @endphp
                                <img src="{{ $imageSrcStep }}" alt="logo" width="100px" height="auto"> <br>
                            @endif

                            <!-- Display the first approver's name -->
                            <strong>Name:</strong> {{ $step->reviewer->name ?? '-' }} <br>

                            <!-- Display the first approver's designation -->
                            <strong>Designation:</strong> {{ $step->reviewer->designation->name ?? '-' }} <br>

                            <!-- Display the first approver's approval date -->
                            <strong>Date:</strong>
                            {{ $step->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }} <br>
                        </div>
                    @endif
                @endforeach
            </td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;">
                <hr>
            </td>
        </tr>
        <tr>
            <td colspan="12" style="background: #ff0;">HR Department</td>
        </tr>

        <tr>
            <td colspan="6" style="font-size:14px;"><strong>Documents verified for the Period of
                    Workdone/Supply:</strong></td>
            <td colspan="6" style="font-size:14px;">{{ $note->documents_workdone_supply }}</td>
        </tr>
        <tr>
            <td colspan="5" style="font-size:14px;"><strong>Whether all the documents required submitted?:</strong>
                {{ $note->required_submitted == 'Y' ? 'Yes' : 'NO' }} </td>
            <td colspan="8" style="font-size:14px;"><strong>Documents discrepancy:</strong>
                {{ $note->documents_discrepancy }}</td>
        </tr>
        <tr>
            <td colspan="6" style="font-size:14px;"><strong>Amount to be retained for non submission/non compliance
                    of
                    HR:</strong> {{ $note->amount_submission_non }}</td>
            <td colspan="6" style="font-size:14px;"><strong>Remarks:</strong> {{ $note->remarks }}</td>
        </tr>
        <tr>
            <td colspan="6" style="font-size:14px;">
                {{-- @foreach ($note->approvalLogs as $index => $step)
                    @if ($userStepRoles->contains('Hr And Admin'))
                        @if ($step->reviewer->file)
                            <img src="{{ $step->reviewer->file ? asset('uploads/' . $step->reviewer->file) : '' }}"
                                alt="logo" width="130px" height="80px"> <br>
                        @endif
                        {{ $step->reviewer->name ?? '-' }}<br>
                        {{ $step->reviewer->designation->name ?? '-' }}<br>
                        {{ $step->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y H:i A') ?? '-' }}<br>
                    @endif
                @endforeach --}}
                @foreach ($note->approvalLogs as $index => $step)
                    @php
                        // Get logged-in user's roles
                        $userStepRolesAdmin = $step->reviewer->getRoleNames();
                    @endphp
                    @if ($userStepRolesAdmin->contains('Hr And Admin'))
                        <div class="approver-info border p-4 mb-4">
                            <!-- Display the first approver's image -->
                            @if ($step->reviewer->file)
                                {{-- <img src="{{ asset('uploads/' . $step->reviewer->file) }}" alt="logo"
                                    width="130px" height="80px"> <br> --}}
                                @php
                                    $filePathHr = public_path('uploads/' . $step->reviewer->file);
                                    $imageSrcHr = file_exists($filePathHr)
                                        ? asset('uploads/' . $step->reviewer->file)
                                        : asset('uploads/test.png');
                                @endphp
                                <img src="{{ $imageSrcHr }}" alt="logo" width="100px" height="auto"> <br>
                            @endif

                            <!-- Display the first approver's name -->
                            <strong>Name:</strong> {{ $step->reviewer->name ?? '-' }} <br>

                            <!-- Display the first approver's designation -->
                            <strong>Designation:</strong> {{ $step->reviewer->designation->name ?? '-' }} <br>

                            <!-- Display the first approver's approval date -->
                            <strong>Date:</strong>
                            {{ $step->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }} <br>
                        </div>
                    @endif
                @endforeach
            </td>
            <td colspan="6" style="font-size:14px;">
                {{-- @foreach ($note->approvalLogs as $index => $step)
                    @if ($userStepRoles->contains('Auditor'))
                        @if ($step->reviewer->file)
                            <img src="{{ $step->reviewer->file ? asset('uploads/' . $step->reviewer->file) : '' }}"
                                alt="logo" width="130px" height="80px"> <br>
                        @endif
                        {{ $step->reviewer->name ?? '-' }}<br>
                        {{ $step->reviewer->designation->name ?? '-' }}<br>
                        {{ $step->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y H:i A') ?? '-' }}<br>
                    @endif
                @endforeach --}}
                @foreach ($note->approvalLogs as $index => $step)
                    @php
                        // Get logged-in user's roles
                        $userStepRolesAuditor = $step->reviewer->getRoleNames();
                    @endphp
                    @if ($userStepRolesAuditor->contains('Auditor'))
                        <div class="approver-info border p-4 mb-4">
                            <!-- Display the first approver's image -->
                            @if ($step->reviewer->file)
                                {{-- <img src="{{ asset('uploads/' . $step->reviewer->file) }}" alt="logo"
                                    width="130px" height="80px"> <br> --}}
                                @php
                                    $filePathAuditor = public_path('uploads/' . $step->reviewer->file);
                                    $imageSrcAuditor = file_exists($filePathAuditor)
                                        ? asset('uploads/' . $step->reviewer->file)
                                        : asset('uploads/test.png');
                                @endphp
                                <img src="{{ $imageSrcAuditor }}" alt="logo" width="100px" height="auto">
                                <br>
                            @endif

                            <!-- Display the first approver's name -->
                            <strong>Name:</strong> {{ $step->reviewer->name ?? '-' }} <br>

                            <!-- Display the first approver's designation -->
                            <strong>Designation:</strong> {{ $step->reviewer->designation->name ?? '-' }} <br>

                            <!-- Display the first approver's approval date -->
                            <strong>Date:</strong>
                            {{ $step->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }} <br>
                        </div>
                    @endif
                @endforeach

            </td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;">
                <hr>
            </td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"><strong>Expense Approved:</strong></td>
        </tr>
        <tr>
            <td colspan="12" style="font-size:14px;"><strong>If payment approved with Deviation:</strong>
                {{ $note->deviations == 'Y' ? 'Yes' : 'NO' }}
            </td>
        </tr>
        @if ($note->auditor_remarks)
            <tr>
                <td colspan="12" style="background: #ff0;">Auditor Department</td>
            </tr>
            <tr>
                <td colspan="12" style="font-size:14px;"><strong>Remarks :</strong>
                    {{ $note->auditor_remarks ?? '-' }}
                </td>
            </tr>
        @endif

        @if ($note->deviations == 'Y')
            <tr>
                <td colspan="12" style="font-size:14px;"><strong>Remarks :</strong>
                    {{ $note->specify_deviation }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="12" style="font-size:14px;">
                <hr>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="font-size:14px;">
                @if ($approvers->count() > 1)
                    <div class="approver-info border p-4 mb-4">
                        @php
                            $secondApprover = $approvers[1] ?? null;
                        @endphp
                        @if ($secondApprover && $secondApprover->status == 'A')
                            @if ($secondApprover->reviewer->file)
                                {{-- <img src="{{ asset('uploads/' . $secondApprover->reviewer->file) }}" alt="logo"
                                    width="130px" height="80px"> <br> --}}
                                @php
                                    $filePathSecond = public_path('uploads/' . $secondApprover->reviewer->file);
                                    $imageSrcSecond = file_exists($filePathSecond)
                                        ? asset('uploads/' . $secondApprover->reviewer->file)
                                        : asset('uploads/test.png');
                                @endphp
                                <img src="{{ $imageSrcSecond }}" alt="logo" width="100px" height="auto">
                                <br>
                            @endif
                            <strong>Name:</strong> {{ $secondApprover->reviewer->name ?? '-' }} <br>
                            <strong>Designation:</strong> {{ $secondApprover->reviewer->designation->name ?? '-' }}
                            <br>
                            <strong>Date:</strong>
                            {{ $secondApprover->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
                            <br>
                        @endif
                    </div>
                @else
                    _____________________
                @endif


            </td>
            <td colspan="3" style="font-size:14px;">
                @if ($approvers->count() > 2)
                    <div class="approver-info border p-4 mb-4">
                        @php
                            $thirdApprover = $approvers[2] ?? null;
                        @endphp
                        @if ($thirdApprover && $thirdApprover->status == 'A')
                            @if ($thirdApprover->reviewer->file)
                                {{-- <img src="{{ asset('uploads/' . $thirdApprover->reviewer->file) }}" alt="logo"
                                    width="130px" height="80px"> <br> --}}
                                @php
                                    $filePathThird = public_path('uploads/' . $thirdApprover->reviewer->file);
                                    $imageSrcThird = file_exists($filePathThird)
                                        ? asset('uploads/' . $thirdApprover->reviewer->file)
                                        : asset('uploads/test.png');
                                @endphp
                                <img src="{{ $imageSrcThird }}" alt="logo" width="100px" height="auto">
                                <br>
                            @endif
                            <strong>Name:</strong> {{ $thirdApprover->reviewer->name ?? '-' }} <br>
                            <strong>Designation:</strong> {{ $thirdApprover->reviewer->designation->name ?? '-' }} <br>
                            <strong>Date:</strong>
                            {{ $thirdApprover->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
                            <br>
                        @endif
                    </div>
                @else
                    ______________________
                @endif

            </td>
            <td colspan="3" style="font-size:14px;">
                @if ($approvers->count() > 3)
                    <div class="approver-info border p-4 mb-4">
                        @php
                            $fourApprover = $approvers[3] ?? null;
                        @endphp
                        @if ($fourApprover && $fourApprover->status == 'A')

                            @if ($fourApprover->reviewer->file)
                                {{-- <img src="{{ asset('uploads/' . $fourApprover->reviewer->file) }}" alt="logo"
                                    width="130px" height="80px"> <br> --}}
                                @php
                                    $filePathFour = public_path('uploads/' . $fourApprover->reviewer->file);
                                    $imageSrcFour = file_exists($filePathFour)
                                        ? asset('uploads/' . $fourApprover->reviewer->file)
                                        : asset('uploads/test.png');
                                @endphp
                                <img src="{{ $imageSrcFour }}" alt="logo" width="100px" height="auto">
                                <br>
                            @endif
                            <strong>Name:</strong> {{ $fourApprover->reviewer->name ?? '-' }} <br>
                            <strong>Designation:</strong> {{ $fourApprover->reviewer->designation->name ?? '-' }} <br>
                            <strong>Date:</strong>
                            {{ $fourApprover->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
                            <br>
                        @endif
                    </div>
                @else
                @endif
            </td>
            <td colspan="3" style="font-size:14px;">
                @if ($approvers->count() > 4)
                    <div class="approver-info border p-4 mb-4">
                        @php
                            $fiveApprover = $approvers[4] ?? null;
                        @endphp

                        @if ($fiveApprover && $fiveApprover->status == 'A')

                            @if ($fiveApprover->reviewer->file)
                                {{-- <img src="{{ asset('uploads/' . $fiveApprover->reviewer->file) }}" alt="logo"
                                    width="130px" height="80px"> <br> --}}
                                @php
                                    $filePathFive = public_path('uploads/' . $fiveApprover->reviewer->file);
                                    $imageSrcFive = file_exists($filePathFive)
                                        ? asset('uploads/' . $fiveApprover->reviewer->file)
                                        : asset('uploads/test.png');
                                @endphp
                                <img src="{{ $imageSrcFive }}" alt="logo" width="100px" height="auto">
                                <br>
                            @endif
                            <strong>Name:</strong> {{ $fiveApprover->reviewer->name ?? '-' }} <br>
                            <strong>Designation:</strong> {{ $fiveApprover->reviewer->designation->name ?? '-' }} <br>
                            <strong>Date:</strong>
                            {{ $fiveApprover->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
                            <br>
                        @endif
                    </div>
                @else
                @endif
            </td>
            <td colspan="3" style="font-size:14px;">
                @if ($approvers->count() > 5)
                    <div class="approver-info border p-4 mb-4">
                        @php
                            $sixApprover = $approvers[5] ?? null;
                        @endphp

                        @if ($sixApprover && $sixApprover->status == 'A')

                            @if ($sixApprover->reviewer->file)
                                {{-- <img src="{{ asset('uploads/' . $sixApprover->reviewer->file) }}" alt="logo"
                                    width="130px" height="80px"> <br> --}}
                                @php
                                    $filePathFive = public_path('uploads/' . $sixApprover->reviewer->file);
                                    $imageSrcFive = file_exists($filePathFive)
                                        ? asset('uploads/' . $sixApprover->reviewer->file)
                                        : asset('uploads/test.png');
                                @endphp
                                <img src="{{ $imageSrcFive }}" alt="logo" width="100px" height="auto">
                                <br>
                            @endif
                            <strong>Name:</strong> {{ $sixApprover->reviewer->name ?? '-' }} <br>
                            <strong>Designation:</strong> {{ $sixApprover->reviewer->designation->name ?? '-' }} <br>
                            <strong>Date:</strong>
                            {{ $sixApprover->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
                            <br>
                        @endif
                    </div>
                @else
                @endif
            </td>
        </tr>
    </table>

</body>

</html>
