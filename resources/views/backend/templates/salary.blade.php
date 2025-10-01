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
                                        <td colspan="3">NHIPPL/Staff Salary</td>
                                        <td colspan="3">Date: 28-04-2023</td>

                                    </tr>
                                    <tr>
                                        <td colspan="6">The Senior Manager </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">State Bank of India</td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">New Delhi-110001</td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">Sub: Transfer of fund from our A/c No.
                                            <strong>40797007409</strong>
                                            BY RTGS/NEFT</td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">Dear Sir,</td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">Please transfer the Sum of Rs. <strong>0.00</strong> No Rupees
                                            Only
                                            from our Common Pool A/c No. <strong>40797007409</strong> to various accounts as
                                            per
                                            details below.</td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">Sr No</td>
                                        <td style="border: 1px solid #ddd;" colspan="1">Name of the Bank</td>
                                        <td style="border: 1px solid #ddd;" colspan="1">Beneficiary Bank’s IFSC Code</td>
                                        <td style="border: 1px solid #ddd;" colspan="1">Account Number of Beneficiary
                                        </td>
                                        <td style="border: 1px solid #ddd;" colspan="1">Name of Beneficiary</td>
                                        <td style="border: 1px solid #ddd;" colspan="1">Amount</td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">1</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">2</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">3</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">4</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">5</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">6</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">7</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">8</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">9</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">10</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">11</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">12</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">13</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">14</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">15</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">16</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">17</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">18</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">19</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">20</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">21</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">22</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">23</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">24</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">25</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">26</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">27</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">28</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">29</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">30</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">31</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">32</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">33</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">34</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">35</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">36</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">37</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">38</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">39</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">40</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">41</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">42</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">43</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">44</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">45</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">46</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">47</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">48</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">49</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">50</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">51</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">52</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">53</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="1">54</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                        <td style="border:1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="1">55</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1">#N/A</td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"></td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #ddd;" colspan="5"><strong> Total</strong></td>
                                        <td style="border: 1px solid #ddd;" colspan="1"><strong> 123971280</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">“We undertake to indemnify SBI Bank for any losses that SBI Bank
                                            may
                                            incur by relying on these instructions”</td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">Thanking you,</td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">For National Highways Infra Projects Private Limited</td>
                                    </tr>
                                    <tr>
                                        <td style="border:1px solid #ddd;" colspan="3">Auth. Signatory<br><br><br></td>
                                        <td style="border:1px solid #ddd;" colspan="3">Auth. Signatory<br><br><br></td>
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
