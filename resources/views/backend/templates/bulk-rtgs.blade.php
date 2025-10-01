@extends('backend.layouts.app')
@section('content')
@php
    $slno = request()->route('slno');
@endphp
    <div class="pagetitle">
        <h1>Blank Page {{ $slno ?? 'N/A' }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item">Pages</li>
                <li class="breadcrumb-item active">Blank</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Import Payment Excel File</h5>
                        <p>This is an examle page with no contrnt. You can use it as a starter for your custom pages.</p>
                        <html>

                        <head></head>

                        <body>
                            <form action="{{route('backend.templates.bulk-rtgs-generate-pdf', $slno)}}" method="post">
                                @csrf
                                <table style="border: 1px solid #ddd;">
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="3"><strong>Application for RTGS/NEFT Remittance</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1"><strong>Debit Account Name</strong></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"><strong>Debit Account Number</strong></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"><strong>Amount</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">x</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">x</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">x</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">x</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">x</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="2"><strong>Total </strong></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"><strong>0000000</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="3">Please remit a sum of Rs. <strong>0.00</strong> ( No Rupees Only) from our above-mentioned account as detailed below:</td>
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
                                        <td style="border: 1px solid #ddd;" colspan="3">1. All payment instructions should be carefully checked by the remitter. As crediting the proceeds of the remittance is based on the beneficiary’s account number, the name of the other bank and its branch being correctly provided, SBI will not be responsible if these particulars are not provided correctly by the remitter.<br>2. Application/Message received after the business hours will be sent on the immediate next working day.<br>3. SBI shall not be responsible for any delay in the processing of the payment due to RBI RTGS system NOT being available/failure of internal communication system at the recipient bank/branch/ incorrect information provided by the remitter/any incorrect credit accorded by the recipient bank/branch due to incorrect information provided by the remitter.<br>4.(i)Remitting branch shall not be liable for any loss or damage arising or resulting from delay in transmission delivery or non-delivery of electronic message or any mistake, omission or error in transmission or delivery thereof or in encrypting/decrypting the message for any cause whatsoever or from the misinterpretation when received or for the action of the destination bank or for any act beyond the control of SBI.<br>(ii) If the recipient branch is closed for any reason, the account shall be credited on the immediate next working day.<br>(iii) Bank is free to recover charges if any in respect of remittances returned on account of faulty/inadequate information.<br>5. We have fully read the terms and conditions of the RTGS/NEFT remittances and shall abide by the same.<br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1"><strong>Signature of the applicant(s)</strong></td>
                                        <td style="border: 1px solid #ddd;" colspan="2"><strong>For National Highways Infra Projects Private Limited</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="2">
                                            <br><br><br><br><br>
                                            Authorised Signatory &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Authorised Signatory</td>
                                    </tr>
                                </table>
                                <div class="row mb-3 mt-3" bis_skin_checked="1">
                                    <div class="col-sm-10" bis_skin_checked="1">
                                        <button type="submit" class="btn btn-primary">Generate PDF</button>
                                    </div>
                                </div>
                            </form>
                        </body>

                        </html>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
