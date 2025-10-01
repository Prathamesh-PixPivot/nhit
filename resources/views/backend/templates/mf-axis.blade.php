@extends('backend.layouts.app')
@section('content')
    <div class="pagetitle">
        <h1>Blank Page {{ request()->route('slno') ?? 'N/A' }}</h1>
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
                            <form action="" method="post">
                                @csrf
                                <table style="border: 1px solid #ddd;">
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="3">To</td>
                                        <td style="*border: 1px solid #ddd;" colspan="2">Date: 21-03-2023</td>

                                    </tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5">Axis Asset Management Company
                                            Limited</td>
                                    </tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5">New Delhi </td>
                                    </tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5">Dear Sir/Mam,</td>
                                    </tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5">Sub : Purchase of Units of Axis
                                            Overnight Mutual Funds</td>
                                    </tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5">With reference to the above
                                            matter
                                            we would like to purchase units for <strong>Rs. 0.00</strong> No Rupees Only
                                            under
                                            Axis Mutual Fund Collection A/C against the following folio numbers:- </td>

                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">S. No.</td>
                                        <td style="border: 1px solid #ddd;" colspan="1">From Investment Account Number
                                        </td>
                                        <td style="border: 1px solid #ddd;" colspan="1">From Project Name</td>
                                        <td style="border: 1px solid #ddd;" colspan="1">Folio Number</td>
                                        <td style="border: 1px solid #ddd;" colspan="1">Amount</td>

                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">1</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border: 1px solid #ddd;" colspan="1">797879821798</td>

                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">2</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>

                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">3</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>

                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">4</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>

                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">5</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>

                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">6</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>

                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">7</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>

                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">8</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>

                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="4">Total</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"> - </td>

                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="5">We are effecting the fund
                                            transfer
                                            for the above in following account:-</td>

                                    </tr>
                                    <tr>

                                        <td style="border: 1px solid #ddd;" colspan="1">Beneficiary Name</td>
                                        <td style="border: 1px solid #ddd;" colspan="4">Axis Mutual Fund Collection A/C
                                        </td>

                                    </tr>
                                    <tr>

                                        <td style="border: 1px solid #ddd;" colspan="1">Beneficiary A/c Number</td>
                                        <td style="border: 1px solid #ddd;" colspan="4">31624650950</td>

                                    </tr>
                                    <tr>

                                        <td style="border: 1px solid #ddd;" colspan="1">IFSC Code</td>
                                        <td style="border: 1px solid #ddd;" colspan="4">SBIN0011777</td>

                                    </tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5"> Kindly effect the mutual fund
                                            transaction.</td>

                                    </tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5">Thanking you,</td>
                                    </tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5">Yours Sincerely,</td>
                                    </tr>
                                    <tr>
                                        <td style="*border: 1px solid #ddd;" colspan="5">For National Highways Infra
                                            Projects Private Limited</td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="2">Auth. Signatory <br><br><br>
                                        </td>
                                        <td style="border: 1px solid #ddd;" colspan="3">Auth. Signatory <br><br><br>
                                        </td>

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
