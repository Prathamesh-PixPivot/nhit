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

        <table style="border: 0px solid #000; width:100%;" cellspacing="0" cellpadding="0">
            <tr>
                <td style="border: 0px solid #000; font-size: 10px; float: none; text-align: center;" colspan="7">
                    <strong>{{ config('app.full_name') }}</strong><br>
                    Registered Office: G-5 6, Sector-10, Dwarka, New Delhi-110075<br>
                    CIN: {{ config('app.cin_no') }}
                </td>
            </tr>
            <tr>
                <td style="border: 0px solid #000;font-size: 10px;" colspan="5">
                    <strong>{{ config('app.note_icon') }}{{ \Carbon\Carbon::now()->format('y') }}
                        {{ $data[0]?->sl_no ?? '' }}</strong>
                </td>

                <td style="border: 0px solid #000; font-size: 10px;" colspan="1">
                    <strong>Date: </strong>
                </td>
                <td style="border: 0px solid #000; font-size: 10px;" colspan="1"> &nbsp;</td>
            </tr>
            <tr>
                <td style="border: 0px solid #000;font-size: 10px;" colspan="7">
                    <br><br>
                    The Senior Manager<br>
                    State Bank of India<br>
                    New Delhi-110001
                </td>
            </tr>
            <tr>
                <td style="border: 0px solid #000;font-size: 10px;" colspan="7">
                    <br><br>
                    <strong>Sub: Transfer of fund from our A/c No. {{ $data[0]->full_account_number ?? '' }} BY
                        RTGS/NEFT</strong>
                    <br><br>
                </td>
            </tr>
            <tr>
                <td style="border: 0px solid #000;font-size: 10px;" colspan="7">
                    Dear Sir,<br>
                    Please transfer the Sums as mentioned below to the various accounts as per details given.
                </td>
            </tr>
        </table>
        <table style="border: 1px solid #000; width:100%;" cellspacing="0" cellpadding="0">
            <tr style="border: 1px solid #000;">
                <td style="border: 1px solid #000;font-size: 10px;" colspan="1"><strong>Sr No.</strong></td>
                <td style="border: 1px solid #000;font-size: 10px;" colspan="1"><strong>Name of the Bank</strong>
                </td>
                <td style="border: 1px solid #000;font-size: 10px;" colspan="1"><strong>Beneficiary Bank's IFSC
                        Code</strong></td>
                <td style="border: 1px solid #000;font-size: 10px;" colspan="1"><strong>Account Number of
                        Beneficiary</strong></td>
                <td style="border: 1px solid #000;font-size: 10px;" colspan="1"><strong>Name of Beneficiary</strong>
                </td>
                <td style="border: 1px solid #000;font-size: 10px;" colspan="1"><strong>Purpose</strong></td>
                <td style="border: 1px solid #000;font-size: 10px;" colspan="1"><strong>Amount</strong></td>
            </tr>
            @php
                $i = 1;
                $total = 0;
            @endphp
            @foreach ($data as $row)
                @php
                    $total += $row->amount;
                @endphp
                <tr style="border: 1px solid #000;">
                    <td style="border: 1px solid #000;font-size: 10px;" colspan="1">{{ $i }}</td>
                    <td style="border: 1px solid #000;font-size: 10px;" colspan="1">
                        {{-- <strong>{{\App\Models\Vendor::where('account_number', $row->full_account_number)->first()?->benificiary_name ?? ''}}</strong> --}}
                        <strong>{{ \App\Models\Vendor::where('account_number', $row->account_number)->first()?->name_of_bank ?? '' }}</strong>
                    </td>
                    <td style="border: 1px solid #000;font-size: 15px;" colspan="1">
                        <strong>{{ \App\Models\Vendor::where('account_number', $row->account_number)->first()?->ifsc_code ?? '' }}</strong>
                    <td style="border: 1px solid #000;font-size: 15px;" colspan="1">
                        <strong>{{ $row->account_number ?? '' }}</strong>
                    </td>
                    </td>
                    <td style="border: 1px solid #000;font-size: 15px;" colspan="1">
                        <strong>{{ $row->name_of_beneficiary ?? '' }}</strong>
                    </td>
                    <td style="border: 1px solid #000;font-size: 10px;" colspan="1">{{ $row->purpose ?? '' }}</td>
                    <td style="border: 1px solid #000;font-size: 15px; text-align:right;" colspan="1">
                        <strong>{{ $row->amount ? \App\Helpers\Helper::formatIndianNumber($row->amount) : '' }}</strong>
                    </td>
                </tr>
                @php
                    $i++;
                @endphp
            @endforeach
            <tr style="border: 1px solid #000;">
                <td style="border: 1px solid #000;font-size: 15px;" colspan="6">Total</td>
                <td style="border: 1px solid #000;font-size: 15px; text-align:right;" colspan="1">
                    <strong>{{ $row->amount ? \App\Helpers\Helper::formatIndianNumber($total) : 0.0 }}</strong>
                </td>
            </tr>
        </table>
        <table style="border: 0px solid #000; width:100%;" cellspacing="0" cellpadding="0">
            <tr>
                <td style="font-size: 5px;" colspan="7">
                    <p><strong>CONDITIONS FOR TRANSFER</strong><br>
                        1. All payment instructions should be carefully checked by the remitter. As crediting the
                        proceeds
                        of the remittance is based on the beneficiary’s account number, the name of the other bank and
                        its
                        branch being correctly provided, SBI will not be responsible if these particulars are not
                        provided
                        correctly by the remitter.<br>
                        2. Application/Message received after the business hours will be sent
                        on the immediate next working day.<br>
                        3. SBI shall not be responsible for any delay in the
                        processing of the payment due to RBI RTGS system NOT being available/failure of internal
                        communication system at the recipient bank/branch/ incorrect information provided by the
                        remitter/any incorrect credit accorded by the recipient bank/branch due to incorrect information
                        provided by the remitter.<br>
                        4.(i)Remitting branch shall not be liable for any loss or damage
                        arising or resulting from delay in transmission delivery or non-delivery of electronic message
                        or
                        any mistake, omission or error in transmission or delivery thereof or in encrypting/decrypting
                        the
                        message for any cause whatsoever or from the misinterpretation when received or for the action
                        of
                        the destination bank or for any act beyond the control of SBI.<br>
                        (ii) If the recipient branch is
                        closed for any reason, the account shall be credited on the immediate next working day.<br>
                        (iii)
                        Bank is free to recover charges if any in respect of remittances returned on account of
                        faulty/inadequate information.<br>
                        5. We have fully read the terms and conditions of the RTGS/NEFT
                        remittances and shall abide by the same.<br>
                        6. The said payment is in accordance with the waterfall
                        mechanisim as mentioned in Escrow Agreement.
                        <br><br>
                    </p>
                </td>
            </tr>
            <tr>
                <td colspan="7" style="font-size: 10px;">

                    Thanking you,<br>
                    <strong>For {{ config('app.full_name') }}</strong>
                    <br><br><br><br><br>

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
                                <img src="{{ $imageSrcFirst }}" alt="logo" width="100px" height="auto"> <br>
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

    </body>

    </html>
@endif
