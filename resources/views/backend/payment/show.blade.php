@extends('backend.layouts.app')
@section('content')
    {{-- <div class="pagetitle">
        <h1>Blank Page</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item">Pages</li>
                <li class="breadcrumb-item active">Blank</li>
            </ol>
        </nav>
    </div><!-- End Page Title --> --}}
    @php
        $helper = new \App\Helpers\Helper();
    @endphp
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        @can('create-role')
                            <a href="{{ route('backend.payments.index') }}" class="btn btn-outline-success btn-sm my-2"><i
                                    class="bi bi-list"></i> Payment List</a>
                        @endcan
                        <h5 class="card-title">Details Of SL No. ({{$payment->sl_no ?? 'N/A'}})</h5>
                        <!--<p>This is an examle page with no contrnt. You can use it as a starter for your custom pages.</p>-->

                    </div>

                </div>

                <div class="card">
                    <div class="card-header">
                        View Request
                    </div>
                    <div class="card-body">
                        <form name="requestFormCreate" method="post" id="requestFormCreate">
                            @csrf
                            <div class="col-md-12 p-2 p-10">
                                <label for="template_type" class="form-label">Template Type</label>
                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('from_account_no') is-invalid @enderror"
                                        id="from_account_no" value="{{$payment->template_type ?? 'N/A'}}"
                                        name="from_account_no" data-index="0" readonly>
                                </div>
                            </div>
                            <div class="col-md-6 p-2 p-10 left-align">
                                <label for="from_account" class="form-label" data-pattern-text="">Payment
                                    From</label>
                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('from_account_no') is-invalid @enderror"
                                        id="from_account_no" value="{{$payment->account_full_name ?? 'N/A'}}"
                                        name="from_account_no" data-index="0" readonly>
                                </div>
                            </div>
                            <div class="col-md-6 p-2 p-10 left-align">
                                <label for="from_account_no" class="form-label">From Account A/C
                                    No.</label>
                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('from_account_no') is-invalid @enderror"
                                        id="from_account_no" value="{{$payment->full_account_number ?? 'N/A'}}"
                                        name="from_account_no" data-index="0" readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-6 p-2 p-10 left-align">
                                <label for="vendor_name" class="form-label">Name</label>
                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('from_account_no') is-invalid @enderror"
                                        id="from_account_no" value="{{$payment->to ?? 'N/A'}}"
                                        name="from_account_no" data-index="0" readonly>
                                </div>
                            </div>
                            <div class="col-md-6 p-2 p-10 left-align">
                                <label for="vendor_account" class="form-label">A/C No.</label>
                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('from_account_no') is-invalid @enderror"
                                        id="from_account_no" value="{{$payment->to_account_type ?? 'N/A'}}"
                                        name="from_account_no" data-index="0" readonly>
                                </div>
                            </div>
                            <div class="col-md-6 p-2 p-10 left-align">
                                <label for="benificiary_name" class="form-label">Benificiary Name</label>
                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('from_account_no') is-invalid @enderror"
                                        id="from_account_no" value="{{$payment->name_of_beneficiary ?? 'N/A'}}"
                                        name="from_account_no" data-index="0" readonly>
                                </div>
                            </div>
                            <div class="col-md-6 p-2 p-10 left-align">
                                <label for="from_account_type" class="form-label">Account Type</label>
                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('from_account_no') is-invalid @enderror"
                                        id="from_account_no" value="{{$payment->to_account_type ?? 'N/A'}}"
                                        name="from_account_no" data-index="0" readonly>
                                </div>
                            </div>
                            <div class="col-md-6 p-2 p-10 left-align">
                                <label for="amount" class="form-label">Amount</label>
                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('from_account_no') is-invalid @enderror"
                                        id="from_account_no" value="{{$payment->amount ?? 'N/A'}}"
                                        name="from_account_no" data-index="0" readonly>
                                </div>
                            </div>
                            <div class="col-md-12 p-2 p-10">
                                <label for="vendor_0_purpose" class="form-label">Purpose</label>
                                <div class="col-md-12">
                                    <textarea name="purpose" id="" class="form-control @error('purpose') is-invalid @enderror" id="purpose" readonly>{{$payment->purpose ?? 'N/A'}}</textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
            </div>
    </section>
@endsection
@push('script')
    <style>
        form .error {
            color: #ff0000;
        }

        .badge {
            cursor: pointer;
        }

        span.badge.badge-primary.badge-pill {
            background: #6c757d;
        }

        .left-align {
            float: left;
            /* padding: 10px; */
        }

        fieldset.scheduler-border {
            border: 1px groove #ddd !important;
            padding: 0 1.4em 1.4em 1.4em !important;
            margin: 0 0 1.5em 0 !important;
            -webkit-box-shadow: 0px 0px 0px 0px #000;
            box-shadow: 0px 0px 0px 0px #000;
        }

        legend.scheduler-border {
            font-size: 1.2em !important;
            font-weight: bold !important;
            text-align: left !important;
            width: auto;
            padding: 0 10px;
            border-bottom: none;
        }

        input:read-only,
        textarea:read-only {
            background-color: #ccc;
        }
        .btnRequestForm.show{
            display: block;
        }
        .btnRequestForm.hide{
            display: none;
        }
    </style>
@endpush
