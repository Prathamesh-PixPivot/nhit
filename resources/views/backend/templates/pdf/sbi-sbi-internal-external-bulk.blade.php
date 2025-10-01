<?php
function convertNumberToWordsForIndia($number, $doOtherWords = true)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = [];
    $words = [
        0 => '',
        1 => 'One',
        2 => 'Two',
        3 => 'Three',
        4 => 'Four',
        5 => 'Five',
        6 => 'Six',
        7 => 'Seven',
        8 => 'Eight',
        9 => 'Nine',
        10 => 'Ten',
        11 => 'Eleven',
        12 => 'Twelve',
        13 => 'Thirteen',
        14 => 'Fourteen',
        15 => 'Fifteen',
        16 => 'Sixteen',
        17 => 'Seventeen',
        18 => 'Eighteen',
        19 => 'Nineteen',
        20 => 'Twenty',
        30 => 'Thirty',
        40 => 'Forty',
        50 => 'Fifty',
        60 => 'Sixty',
        70 => 'Seventy',
        80 => 'Eighty',
        90 => 'Ninety',
    ];

    $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];

    while ($i < $digits_length) {
        $divider = $i == 2 ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = ($counter = count($str)) && $number > 9 ? 's' : null;
            $hundred = $counter == 1 && $str[0] ? ' and ' : null;
            $str[] = $number < 21 ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
        } else {
            $str[] = null;
        }
    }

    $Rupees = implode('', array_reverse($str));
    $paise = $decimal > 0 ? '.' . ($words[$decimal / 10] . ' ' . $words[$decimal % 10]) . ' Paise' : '';

    return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
}

?>
@if (!empty($data) && $data->isNotEmpty())
    <html>

    <head></head>

    <body>
        <table style="border: 0px solid #000;" cellspacing="0">
            <tr>
                <td style="border: 0px solid #000; font-size: 15px; float: none; text-align: center;" colspan="7">
                    <strong>NHIT WESTERN PROJECTS PRIVATE LIMITED</strong><br>
                    (Formerly know as NATIONAL HIGHWAYS INFRA PROJECTS PRIVATE LIMITED)<br>
                    Registered Office: G-5 6, Sector-10, Dwarka, New Delhi-110075<br>
                    CIN: U45202DL2020PTC366737
                </td>
            </tr>
            <tr>
                <td style="border: 0px solid #000;font-size: 10px;" colspan="5">
                    <strong>{{ config('app.note_icon') }}{{ \Carbon\Carbon::now()->format('y') }}
                        {{ $data[0]?->sl_no ?? '' }}</strong>
                </td>
                <td style="border: 0px solid #000; text-align: left; float: left;font-size: 10px;" colspan="2">
                    <strong>Date: </strong>
                </td>
            </tr>
            <tr>
                <td style="border: 0px solid #000;font-size: 15px;" colspan="7">
                    The Senior Manager<br>
                    State Bank of India<br>
                    New Delhi-110001
                </td>
            </tr>
            <tr>
                <td style="border: 0px solid #000;font-size: 15px;" colspan="7">
                    <strong>Sub: Transfer of funds</strong>
                </td>
            </tr>
            <tr>
                <td style="border: 0px solid #000;font-size: 15px;" colspan="7">
                    Dear Sir,<br>
                    Please transfer the Sums as mentioned below to the various accounts as per details given. <br><br>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;font-size: 10px;" colspan="1">Sr No.</td>
                <td style="border: 1px solid #000;font-size: 10px;" colspan="1">From Account Name</td>
                <td style="border: 1px solid #000;font-size: 10px;" colspan="1">From Account Number</td>
                <td style="border: 1px solid #000;font-size: 10px;" colspan="1">To Account Name</td>
                <td style="border: 1px solid #000;font-size: 10px;" colspan="1">To Account Number</td>
                <td style="border: 1px solid #000;font-size: 10px;" colspan="1">Purpose</td>
                <td style="border: 1px solid #000;font-size: 10px;" colspan="1">Amount</td>
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
                    <td style="border: 1px solid #000;font-size: 13px;" colspan="1">{{ $i }}</td>
                    <td style="border: 1px solid #000;font-size: 15px;" colspan="1">
                        <strong>{{ \App\Models\Vendor::where('account_number', $row->full_account_number)->first()?->benificiary_name ?? '' }}</strong>
                    </td>
                    <td style="border: 1px solid #000;font-size: 15px;" colspan="1">
                        <strong>{{ $row->full_account_number ?? '' }}</strong>
                    </td>
                    <td style="border: 1px solid #000;font-size: 15px;" colspan="1">
                        <strong>{{ $row->name_of_beneficiary ?? '' }}</strong>
                    </td>
                    <td style="border: 1px solid #000;font-size: 15px;" colspan="1">
                        <strong>{{ $row->account_number ?? '' }}</strong>
                    </td>
                    {{-- <td style="border: 1px solid #000;font-size: 13px;" colspan="1"><strong>{{$row->name_of_bank ?? ''}}</strong></td> --}}
                    {{-- <td style="border: 1px solid #000;font-size: 10px;" colspan="1"><strong> </strong></td> --}}
                    <td style="border: 1px solid #000;font-size: 10px;" colspan="1">{{ $row->purpose ?? '' }}</td>
                    <td style="border: 1px solid #000;font-size: 15px; text-align:right;" colspan="1">
                        <strong>{{ $row->amount ? \App\Helpers\Helper::formatIndianNumber($row->amount) : '' }}</strong>
                    </td>
                    {{-- <td style="border: 1px solid #000;font-size: 20px;" colspan="1"><strong>{{$row->amount ? convertNumberToWordsForIndia(456456.22) : ''}}</strong></td> --}}
                </tr>
                @php
                    $i++;
                @endphp
            @endforeach
            <tr>
                <td style="border: 1px solid #000;font-size: 10px;" colspan="1">Total</td>
                <td style="border: 1px solid #000;font-size: 10px;" colspan="1"> </td>
                <td style="border: 1px solid #000;font-size: 20px;" colspan="1"> </td>
                <td style="border: 1px solid #000;font-size: 20px;" colspan="1"> </td>
                <td style="border: 1px solid #000;font-size: 10px;" colspan="1"> </td>
                <td style="border: 1px solid #000;font-size: 10px;" colspan="1"> </td>
                <td style="border: 1px solid #000;font-size: 20px; text-align:right;" colspan="1">
                    <strong>{{ $row->amount ? \App\Helpers\Helper::formatIndianNumber($total) : 0.0 }}</strong>
                </td>
            </tr>
            <tr>
                <td style="border: 0px solid #000; font-size: 5px;" colspan="7">
                    <br><br>
                    1. All payment instructions should be carefully checked by the remitter. As crediting the proceeds
                    of the remittance is based on the beneficiary’s account number, the name of the other bank and its
                    branch being correctly provided, SBI will not be responsible if these particulars are not provided
                    correctly by the remitter.<br>2. Application/Message received after the business hours will be sent
                    on the immediate next working day.<br>3. SBI shall not be responsible for any delay in the
                    processing of the payment due to RBI RTGS system NOT being available/failure of internal
                    communication system at the recipient bank/branch/ incorrect information provided by the
                    remitter/any incorrect credit accorded by the recipient bank/branch due to incorrect information
                    provided by the remitter.<br>4.(i)Remitting branch shall not be liable for any loss or damage
                    arising or resulting from delay in transmission delivery or non-delivery of electronic message or
                    any mistake, omission or error in transmission or delivery thereof or in encrypting/decrypting the
                    message for any cause whatsoever or from the misinterpretation when received or for the action of
                    the destination bank or for any act beyond the control of SBI.<br>(ii) If the recipient branch is
                    closed for any reason, the account shall be credited on the immediate next working day.<br>(iii)
                    Bank is free to recover charges if any in respect of remittances returned on account of
                    faulty/inadequate information.<br>5. We have fully read the terms and conditions of the RTGS/NEFT
                    remittances and shall abide by the same.<br>6. The said payment is in accordance with the waterfall
                    mechanisim as mentioned in Escrow Agreement.
                    <br><br>

                </td>
            </tr>
            <tr>
                <td style="border: 0px solid #000; font-size: 15px;" colspan="7">
                    Thanking you,<br>
                    <strong>For {{ config('app.full_name') }}</strong>

                    @php
                        $sl_no = $data[0]?->sl_no;

                        $approvers = \App\Models\BankLetterApprovalLog::with('reviewer')
                            ->where('sl_no', $sl_no)
                            ->where('status', 'A')
                            ->get()
                            ->filter(function ($log) {
                                return $log->reviewer->getRoleNames()->contains('PN Approver');
                            });
                        $approverNames = $approvers->map(function ($log) {
                            return $log->reviewer;
                        });
                    @endphp
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; vertical-align: top; padding: 10px;">
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
                <td colspan="2" style="text-align: center; vertical-align: top; padding: 10px;">

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
                                <img src="{{ $imageSrcFirst }}" alt="logo" width="100px" height="auto">
                                <br>
                            @endif
                        @endif
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center; vertical-align: top; padding: 10px;">
                    Authorized Signatory
                </td>
                <td colspan="2" style="text-align: center; vertical-align: top; padding: 10px;">
                    Authorized Signatory
                </td>
            </tr>
        </table>
        {{-- @foreach ($approvers as $index => $step)
            <div class="d-flex align-items-center position-relative mb-4 mt-5" style="padding-left: 30px;">
                <!-- Step Dot -->
                <div class="position-absolute bg-primary rounded-circle" style="width: 12px; height: 12px; left: 5px;">
                </div>
                <!-- Step Info -->
                <div>
                    <p class="fw-bold mb-1">Step {{ $index + 1 }}</p>
                    <p class="text-muted small">{{ $index == 0 ? 'Maker' : 'Reviewer' }}:
                        {{ $step->reviewer->name }}</p>
                    </p>
                    @if ($step->status == 'R')
                        <p class="text-muted small">Remarks:
                            {{ $step->comments ?? '-' }}</p>
                        </p>
                    @endif
                    <p class="text-muted small">
                        @if ($step->status == 'A')
                            <span class="badge bg-success">Approved</span>
                        @elseif($step->status == 'P')
                            <span class="badge bg-warning text-dark">Draft</span>
                        @elseif($step->status == 'R')
                            <span class="badge bg-danger">Rejected</span>
                        @elseif($step->status == 'S')
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-secondary">Unknown</span>
                        @endif
                    </p>

                    {{ $step->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') }}
                    @if (!$loop->last)
                        <div class="position-absolute start-0 top-100 translate-middle-y border-start border-2"
                            style="height: 30px; left: 6px;"></div>
                    @endif
                </div>
            </div>
        @endforeach

        @if ($approvers->last()?->logPriorities->last()?->priority)
            <div class="d-flex align-items-center position-relative mb-4" style="padding-left: 30px;">
                <div class="position-absolute bg-primary rounded-circle" style="width: 12px; height: 12px; left: 5px;">
                </div>
                <div>
                    <p class="fw-bold mb-1">Next Approver:
                        @foreach ($approvers->last()?->logPriorities as $log)
                            {{ $log->priority->user->name }} ,
                        @endforeach
                    </p>
                </div>
            </div>
        @endif --}}
    </body>

    </html>
@endif
