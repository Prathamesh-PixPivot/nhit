@extends('email.layout.app')
@section('content')
    <td class="wrapper">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <p>Dear {{ $data['supplier_name'] }},</p>
                    <p>
                        @if ($note->greenNote)
                            Invoice No.
                        @elseif ($note->reimbursementNote)
                            Reimbursement Note.
                        @else
                            --
                        @endif {{ $data['invoice_no'] }} dated {{ $data['invoice_date'] }} in favour of
                        {{ $data['entity_name'] }} has been settled through online payment of Rs.
                        {{ number_format($data['amount_paid'], 2) }} on {{ $data['payment_date'] }} via UTR No.
                        {{ $data['utr_no'] }} As per the details below.
                    </p>
                </td>
            </tr>

            <tr>
                <td colspan="12" style="font-size:14px; margin-top: 10px;"><strong>Summary of payment</strong></td>
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
                        {{ \App\Helpers\Helper::formatIndianNumber($data['grossAmount']) }}
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
                        {{ \App\Helpers\Helper::formatIndianNumber($data['grossAmount']) }}
                    @elseif ($note->reimbursementNote)
                        {{ \App\Helpers\Helper::formatIndianNumber($data['grossAmount']) }}
                    @else
                        N/A
                    @endif
                </td>
                <td colspan="5" style="font-size:14px;"></td>
            </tr>



            @foreach ($data['lessParticulars'] as $particular)
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
            @foreach ($data['addParticulars'] as $particular)
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
                    {{ \App\Helpers\Helper::formatIndianNumber($data['netPayable']) }}</td>
                <td colspan="5" style="font-size:14px;"></td>
            </tr>
            <tr>
                <td colspan="4" style="border:1px solid;font-size:14px;margin-bottom: 10px;"><strong>Net Payable Amount
                        (Round
                        Off)</strong></td>
                <!--<td colspan="4" style="border:1px solid;font-size:14px;"></td>-->
                <td colspan="3" style="border:1px solid;font-size:14px; text-align: right;">
                    {{ \App\Helpers\Helper::formatIndianNumber($data['roundedNetPayable']) }}</td>
                <td colspan="5" style="font-size:14px;"></td>
            </tr>



            <tr>
                <td style="margin-top: 10px;">
                    <p>Thanks,</p>
                    <h3>NHIT.</h3>
                </td>
            </tr>
        </table>
    </td>
@endsection
