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
        $result = [
            "1" => "Escrow Account",
            "2" => "Toll Collection Sub account",
            "3" => "Statutory Dues Account",
            "9" => "Concession Fee Account",
            "6" => "Construction Account denominated",
            "4" => "O&M Expenses Account",
            "5" => "Debt Payment Account",
            "11" => "Authority Dues Payment Account",
            "8" => "Major Maintenance Reserve Account",
            "10" => "Surplus Account denominated",
            "7" => "Investment Account denominated",
            "12" => "Enforcement Proceeds Account",
            "13" => "Compensation Proceeds Account",
            "14" => "Insurance Proceeds Account",
            "15" => "Termination Proceeds Account",
            "16" => "Total Condemnation Proceeds Account",
            "18" => "Statutory Payments Pool account",
            "17" => "Common Payments Pool account"
        ]
    @endphp

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <!--<div class="card">-->
                <!--    <div class="card-body">-->
                <!--        @can('create-role')
        -->
                    <!--            <a href="{{ route('backend.ratio.index') }}" class="btn btn-outline-success btn-sm my-2"><i-->
                    <!--                    class="bi bi-list"></i> Ratio List</a>-->
                    <!--
    @endcan-->
                <!--        <h5 class="card-title">Ratio Request</h5>-->
                <!--        <p>This is an examle page with no contrnt. You can use it as a starter for your custom pages.</p>-->
                <!--    </div>-->
                <!--</div>-->
                <div class="card">
                    <div class="card-header">
                        New Ratio Request
                    </div>

                    {{-- <div class="card-body">

                        <div class="tablecontainer">

                            <p style="color:red;">(*) All fields mandatory</p>

                            <table id="requestFormtable" class="table-responsive-full sort-table">

                                <thead>

                                    <tr>

                                        <th>Template Type</th>

                                        <th>Internal/External</th>

                                        <th>Project <i class="fas fa-sort"></i></th>

                                        <th>Payment From<i class="fas fa-sort"></i></th>

                                        <th>From Account (A/C)<i class="fas fa-sort"></i></th>

                                        <th>Payment To <i class="fas fa-sort"></i></th>

                                        <th>A/C No.<i class="fas fa-sort"></i></th>

                                        <th>Benificiary Name <i class="fas fa-sort"></i></th>

                                        <th>To Account Type <i class="fas fa-sort"></i></th>

                                        <th>Name Of Bank <i class="fas fa-sort"></i></th>

                                        <th>Amount <i class="fas fa-sort"></i></th>

                                        <th>Purpose <i class="fas fa-sort"></i></th>

                                    </tr>

                                </thead>

                                <tbody id="table-body-request">

                                    <tr>

                                        <form name="requestFormCreate" method="post" id="requestFormCreate">

                                            @csrf

                                            <input type="hidden"
                                                class="form-control @error('vendor_name') is-invalid @enderror"
                                                id="vendor_name" value="{{ old('vendor_name') ?? '' }}" name="vendor_name">

                                            <input type="hidden"
                                                class="form-control @error('account_full_name') is-invalid @enderror"
                                                id="account_full_name" value="{{ old('account_full_name') ?? '' }}"
                                                name="account_full_name" readonly>



                                            <input type="hidden"
                                                class="form-control @error('from_account_type') is-invalid @enderror"
                                                id="from_account_type" value="{{ old('from_account_type') ?? '' }}"
                                                name="from_account_type" readonly>

                                            <td>

                                                <div class="col-md-12 p-2 p-10 left-align">

                                                    <label for="internal_external" class="form-label">Internal/External
                                                        <span style="color: red;">*</span></label>

                                                    <div class="col-md-12">

                                                        <select name="internal_external" id="internal_external"
                                                            class="form-control internal_external @error('internal_external') is-invalid @enderror"
                                                            required>

                                                            <option value="">--Select---</option>

                                                            <option
                                                                value="Internal"{{ old('internal_external') == 'Internal' ? 'selected' : '' }}>
                                                                Internal</option>

                                                            <option
                                                                value="External"{{ old('internal_external') == 'External' ? 'selected' : '' }}>
                                                                External</option>

                                                        </select>

                                                        @error('internal_external')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror

                                                    </div>

                                                </div>

                                            </td>
                                            <td>

                                                <div class="col-md-12 p-2 p-10 left-align">

                                                    <label for="template_type" class="form-label">Template Type <span
                                                            style="color: red;">*</span></label>

                                                    <div class="col-md-12">

                                                        <select name="template_type" id="template_type"
                                                            class="form-control template_type @error('template_type') is-invalid @enderror"
                                                            required>

                                                            <option value="">--Select---</option>

                                                            <option value="bulk-rtgs"
                                                                {{ old('template_type') == 'mf-rtgs' ? 'selected' : '' }}>

                                                                Bulk RTGS</option>

                                                            <option value="mf-axis"
                                                                {{ old('template_type') == 'mf-axis' ? 'selected' : '' }}>MF

                                                                Axis</option>

                                                            <option value="mf-kotak"
                                                                {{ old('template_type') == 'mf-kotak' ? 'selected' : '' }}>

                                                                MF Kotak</option>

                                                            <option value="mf-sbi"
                                                                {{ old('template_type') == 'mf-sbi' ? 'selected' : '' }}>MF

                                                                SBI</option>

                                                            <option value="rtgs"
                                                                {{ old('template_type') == 'rtgs' ? 'selected' : '' }}>RTGS

                                                            </option>

                                                            <option value="salary"
                                                                {{ old('template_type') == 'salary' ? 'selected' : '' }}>

                                                                Salary</option>

                                                            <option value="sbi"
                                                                {{ old('template_type') == 'sbi' ? 'selected' : '' }}>SBI

                                                            </option>

                                                        </select>

                                                        @error('template_type')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror

                                                    </div>

                                                </div>

                                            </td>

                                            <td>

                                                <div class="col-md-12 p-2 p-10 left-align">

                                                    <label for="project" class="form-label">Project <span
                                                            style="color: red;">*</span></label>

                                                    <div class="col-md-12">

                                                        <select name="project" id="project"
                                                            class="form-control project @error('project') is-invalid @enderror"
                                                            required>

                                                            <option value="">--Project---</option>

                                                        </select>

                                                        @error('project')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror

                                                    </div>

                                                </div>

                                            </td>

                                            <td>

                                                <div class="col-md-12 p-2 p-10 left-align">

                                                    <label for="from_account" class="form-label"
                                                        data-pattern-text="">Payment

                                                        From <span style="color: red;">*</span></label>

                                                    <div class="col-md-12">

                                                        <select id="from_account"
                                                            class="form-control from_account @error('from_account') is-invalid @enderror"
                                                            name="from_account" data-index="0">

                                                        </select>

                                                        @error('from_account')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror

                                                    </div>

                                                </div>

                                            </td>

                                            <td>

                                                <div class="col-md-12 p-2 p-10 left-align">

                                                    <label for="from_account_no" class="form-label">From Account A/C</label>

                                                    <div class="col-md-12">

                                                        <input type="text"
                                                            class="form-control @error('from_account_no') is-invalid @enderror"
                                                            id="from_account_no"
                                                            value="{{ old('from_account_no') ?? '' }}"
                                                            name="from_account_no" data-index="0" readonly>

                                                        @error('from_account_no')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror

                                                    </div>

                                                </div>

                                            </td>

                                            <td>

                                                <div class="col-md-12 p-2 p-10 left-align">

                                                    <label for="vendor_code" class="form-label">Payment To <span
                                                            style="color: red;">*</span></label>

                                                    <div class="col-md-12">


                                                        <select id="vendor_code"
                                                            class="form-control vendor_code @error('vendor_code') is-invalid @enderror"
                                                            name="vendor_code" data-index="0">



                                                        </select>

                                                        @error('vendor_code')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror

                                                    </div>

                                                </div>

                                            </td>

                                            <td>

                                                <div class="col-md-12 p-2 p-10 left-align">

                                                    <label for="vendor_account" class="form-label">A/C No.</label>

                                                    <div class="col-md-12">

                                                        <input type="text"
                                                            class="form-control @error('vendor_account') is-invalid @enderror"
                                                            id="vendor_account" value="{{ old('vendor_account') ?? '' }}"
                                                            name="vendor_account" readonly>

                                                        @error('vendor_account')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror

                                                    </div>

                                                </div>

                                            </td>

                                            <td>

                                                <div class="col-md-12 p-2 p-10 left-align">

                                                    <label for="benificiary_name" class="form-label">Benificiary

                                                        Name</label>

                                                    <div class="col-md-12">

                                                        <input type="text"
                                                            class="form-control @error('benificiary_name') is-invalid @enderror"
                                                            id="benificiary_name"
                                                            value="{{ old('benificiary_name') ?? '' }}"
                                                            name="benificiary_name" readonly>

                                                        @error('benificiary_name')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror

                                                    </div>

                                                </div>

                                            </td>

                                            <td>

                                                <div class="col-md-12 p-2 p-10 left-align">

                                                    <label for="to_account_type" class="form-label">To Account

                                                        Type</label>

                                                    <div class="col-md-12">

                                                        <input type="text"
                                                            class="form-control @error('to_account_type') is-invalid @enderror"
                                                            id="to_account_type"
                                                            value="{{ old('to_account_type') ?? '' }}"
                                                            name="to_account_type" readonly>

                                                        @error('to_account_type')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror

                                                    </div>

                                                </div>

                                            </td>

                                            <td>

                                                <div class="col-md-12 p-2 p-10 left-align">

                                                    <label for="name_of_bank" class="form-label">Name Of Bank</label>

                                                    <div class="col-md-12">

                                                        <input type="text"
                                                            class="form-control @error('name_of_bank') is-invalid @enderror"
                                                            id="name_of_bank" value="{{ old('name_of_bank') ?? '' }}"
                                                            name="name_of_bank" readonly>

                                                        @error('name_of_bank')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror

                                                    </div>

                                                </div>

                                            </td>

                                            <td>

                                                <div class="col-md-12 p-2 p-10 left-align">

                                                    <label for="amount" class="form-label">Amount</label>

                                                    <div class="col-md-12">

                                                        <input type="number"
                                                            class="form-control @error('amount') is-invalid @enderror"
                                                            id="amount" value="{{ old('amount') ?? '' }}"
                                                            name="amount">

                                                        @error('amount')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror

                                                    </div>

                                                </div>

                                            </td>

                                            <td>

                                                <div class="col-md-12 p-2 p-10 left-align">

                                                    <label for="vendor_0_purpose" class="form-label">Purpose</label>

                                                    <div class="col-md-12">

                                                        <textarea name="purpose" id="" class="form-control @error('purpose') is-invalid @enderror" id="purpose">{{ old('purpose') ?? '' }}</textarea>

                                                        @error('purpose')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror

                                                    </div>

                                                </div>

                                            </td>

                                        </form>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                        <div class="p-2">

                            <button class="btn btn-primary btn-sm float-right add-product">Add In Queue</button>

                            <button class="btn btn-primary btn-sm float-right request-queue-clear">Clear Request

                                Queue</button>

                        </div>



                    </div> --}}

                    <div class="card-body">
                        <form action="{{ route('backend.ratio.store') }}" method="post"
                            class="col-md-12 p-2 p-10 left-align row">
                            @csrf

                            <div class="col-md-4">
                                {{-- <label for="amount" class="form-label">Amount</label> --}}

                                <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                    id="amount" value="{{ old('amount') ?? '' }}" name="amount" placeholder="Amount">

                                @error('amount')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                            </div>
                            <div class="col-md-4">
                                {{-- @php
                                    $froms = \App\Models\Vendor::groupBy('account_name')->get();
                                @endphp
                                <select name="from_ac" id="from_ac"
                                    class="form-control @error('from_ac') is-invalid @enderror">
                                    <option value="">--Select From--</option>
                                    @foreach ($froms as $item)
                                        <option value="{{ $item->account_name }}">{{ $item->account_name }}</option>
                                    @endforeach
                                </select> --}}
                                {{-- <input type="text" class="form-control @error('from_ac') is-invalid @enderror"
                                    id="from_ac" value="{{ old('from_ac', request()->from_ac) ?? '' }}" name="from_ac"
                                    placeholder="From A/c"> --}}
                                <select id="from_ac" class="form-control from_ac @error('from_ac') is-invalid @enderror"
                                    name="from_ac">
                                    <option value="">--Select From---</option>
                                    @foreach (@$result as $sno => $item)
                                        <option value="{{ $sno }}" data-s_no="{{ $sno }}">
                                            {{ $item }}</option>
                                    @endforeach
                                </select>
                                @error('from_ac')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                            </div>
                            <div class="col-md-4">
                                <select name="to_ac" id="to_ac"
                                    class="form-control @error('to_ac') is-invalid @enderror">
                                    <option value="">--Select To--</option>

                                    @foreach (@$result as $sno => $item)
                                    <option value="{{ $sno }}" data-s_no="{{ $sno }}">
                                        {{ $item }}</option>
                                    @endforeach {{-- @foreach ($froms as $item)
                                        <option value="{{ $item->short_name }}">{{ $item->short_name }}</option>
                                    @endforeach --}}
                                </select>
                                {{-- <input type="text" class="form-control @error('to_ac') is-invalid @enderror"
                                    id="to_ac" value="{{ old('to_ac', request()->to_ac) ?? '' }}" name="to_ac"
                                    placeholder="To A/c">

                                @error('to_ac')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror --}}

                            </div>

                            <button type="submit" class="btn btn-primary btn-sm mt-3">Add Amount</button>

                            <!--<button class="btn btn-primary mt-3" type="submit">submit</button>-->
                        </form>
                    </div>
                </div>

                <form class="" action="{{ route('backend.payments.store') }}" method="post" name="">

                    @csrf
                    <div class="list-group mt-5 request-in-queue delete-item">
                        @include('backend.ratio.requestForm', [
                            'vendorItems' => $vendorItems,
                        ])
                    </div>

                    {{-- <div class="col-md-12 mt-5  {{ empty(\Cache::get('cart')) ? 'hide' : 'show' }}"> --}}
                    @if (@$totalDistribution)
                        <div class="d-block mb-2">
                            <button class="col-md-2 offset-md-0 btn btn-primary btn-sm request-form-submit"> Total Amount =
                                {{ $totalDistribution['totalAmount'] }}</button>
                            <button class="col-md-2 offset-md-0 btn btn-primary btn-sm request-form-submit"> Total
                                Distributed =
                                {{ $totalDistribution['totalDistributed'] }}</button>
                            <button class="col-md-2 offset-md-0 btn btn-primary btn-sm request-form-submit"> Remaining
                                Amount =
                                {{ $totalDistribution['remainingAmount'] }}</button>
                        </div>
                    @endif

                    <input type="submit" class="col-md-2 offset-md-0 btn btn-primary btn-sm request-form-submit"
                        value="Save" style="background: #6c757d; color: #fff;">

                    {{-- </div> --}}

                </form>

            </div>

    </section>
@endsection

@push('script')
    <style>
        form .error {

            color: #ff0000;

        }



        form#requestFormCreate {

            clear: both;

            overflow: hidden;

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

            padding: 2px 10px 2px 10px !important;

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

            border: inherit;

        }



        #employee-table input:read-only,

        #employee-table textarea:read-only {

            background-color: transparent;

            border: inherit;

        }



        .btnRequestForm.show {

            display: block;

        }



        .btnRequestForm.hide {

            display: none;

        }



        #requestFormtable .select2-container .select2-results {
            max-height: 200px;
        }

        #requestFormtable .select2-results {
            max-height: 200px;
        }

        #requestFormtable .select2-choices {
            min-height: 150px;
            max-height: 150px;
            overflow-y: auto;
        }

        #requestFormtable .select2.select2-container,

        #requestFormtable input:read-only,

        #requestFormtable input[type=text],

        #requestFormtable input[type=number],

        #requestFormtable textarea,

        #requestFormtable select {

            position: relative;

            z-index: 2;

            float: left;

            width: 250px !important;

            margin-bottom: 0;

            display: table;

            table-layout: fixed;

            height: 50px;

            overflow: hidden;



        }
        .fs-wrap{
                width: 100%;
        }
        .fs-label-wrap, .fs-dropdown{
            line-height: 1.5;
            border-radius: var(--bs-border-radius);
        }
        .fs-dropdown {
            width: 31%;
            z-index: 1;
        }
    </style>

    <script>
     $(document).ready(function() {
            $('#from_ac').fSelect({
                placeholder: 'Select options',
                // numDisplayed: 0,
                overflowText: '{n} selected',
                searchText: 'Search options',
                showSearch: true
            });
            $('#to_ac').fSelect({
                placeholder: 'Select options',
                // numDisplayed: 0,
                overflowText: '{n} selected',
                searchText: 'Search options',
                showSearch: true
            });
    });
        $('.add-product').hide();

        $('#internal_external').select2();

        $('#template_type').select2();

        $('#project').select2();

        $('#project').select2({

            placeholder: 'Search Project Name',

            minimumInputLength: 0,

            minimumResultsForSearch: 1,

            tags: false,

            multiple: false,

            tokenSeparators: [',', ' '],

            // templateResult: formatRepo,

            // templateSelection: formatOptions,

            allowClear: true,

            ajax: {

                url: "{{ route('backend.payments.searchProject') }}",

                type: 'POST',

                cache: true,

                data: function(params) {

                    // let id = $(this).data('id');

                    var query = {

                        search: params.term,

                        // type: 'public',

                        // page: params.page,

                        _token: '{{ csrf_token() }}'

                    }



                    // Query parameters will be ?search=[term]&type=public

                    return query;

                },

                processResults: function(data, params) {

                    // parse the results into the format expected by Select2

                    // since we are using custom formatting functions we do not need to

                    // alter the remote JSON data, except to indicate that infinite

                    // scrolling can be used

                    // params.page = params.page || 1;

                    return {

                        results: $.map(data, function(item) {

                            // console.log("item",item)

                            return {

                                text: item.project,

                                id: item.project,

                                data_id: item.id,

                                s_no: item.s_no,

                                account_number: item.account_number,

                                vendor_name: item.vendor_name,

                                vendor_nick_name: item.vendor_nick_name,

                                benificiary_name: item.benificiary_name,

                                from_account_type: item.from_account_type,

                            }

                        }),

                        /* pagination: {

                            more: (params.page * 1) < data.total

                        } */

                    };

                }

            },

            // I set `staff_constant` field here

            templateSelection: function(data, container) {

                $(data.element).attr('data-id', data.data_id);

                $(data.element).attr('data-ac', data.account_number);

                $(data.element).attr('data-name', data.vendor_name);

                $(data.element).attr('data-s_no', data.s_no);

                $(data.element).attr('data-short_name', data.short_name);

                $(data.element).attr('data-project', data.project);

                $(data.element).attr('data-vendor_nick_name', data.vendor_nick_name);

                $(data.element).attr('data-account_full_name', data.account_full_name);

                $(data.element).attr('data-benificiary', data.benificiary_name);

                $(data.element).attr('data-from_account_type', data.from_account_type);

                return data.text;

            },

        });

        $('#from_account').select2({

            placeholder: 'Search Payment From',

            minimumInputLength: 0,

            minimumResultsForSearch: 1,

            tags: false,

            multiple: false,

            tokenSeparators: [',', ' '],

            // templateResult: formatRepo,

            // templateSelection: formatOptions,

            allowClear: true,

            ajax: {

                url: "{{ route('backend.payments.searchFromVendor') }}",

                type: 'POST',

                cache: true,

                data: function(params) {

                    // let id = $(this).data('id');

                    var query = {

                        search: params.term,

                        // type: 'public',

                        // page: params.page,

                        _token: '{{ csrf_token() }}'

                    }



                    // Query parameters will be ?search=[term]&type=public

                    return query;

                },

                processResults: function(data, params) {

                    // parse the results into the format expected by Select2

                    // since we are using custom formatting functions we do not need to

                    // alter the remote JSON data, except to indicate that infinite

                    // scrolling can be used

                    // params.page = params.page || 1;

                    return {

                        results: $.map(data, function(item) {

                            // console.log("item",item)

                            let project_name = item.project ? item.project + ' ' + item.short_name :

                                item.project

                            let account_full_name = item.project ? item.project + ' ' + item

                                .short_name : item.project

                            let benificiary_name = item.project ? item.project + ' ' + item.short_name :

                                item.project

                            return {

                                text: item.short_name,

                                id: item.short_name,

                                data_id: item.id,

                                s_no: item.s_no,

                                account_number: item.account_number,

                                vendor_name: project_name,

                                vendor_nick_name: item.vendor_nick_name,

                                benificiary_name: item.benificiary_name,

                                from_account_type: item.short_name,

                                account_full_name: account_full_name,

                            }

                        }),

                        /* pagination: {

                            more: (params.page * 1) < data.total

                        } */

                    };

                }

            },

            // I set `staff_constant` field here

            templateSelection: function(data, container) {

                let project_name = data.project ? data.project + ' ' + data.short_name : data.project

                let account_full_name = data.project ? data.project + ' ' + data.short_name : data.project

                let from_account_type = data.short_name

                let benificiary_name = data.project ? data.project + ' ' + data.short_name : data.project

                $(data.element).attr('data-id', data.data_id);

                $(data.element).attr('data-ac', data.account_number);

                $(data.element).attr('data-name', data.vendor_name);

                $(data.element).attr('data-short_name', data.short_name);

                $(data.element).attr('data-project', project_name);

                $(data.element).attr('data-s_no', data.s_no);

                $(data.element).attr('data-vendor_nick_name', data.vendor_nick_name);

                $(data.element).attr('data-account_full_name', account_full_name);

                $(data.element).attr('data-benificiary', data.benificiary_name);

                $(data.element).attr('data-from_account_type', from_account_type);

                return data.text;

            },

        });



        $('#vendor_code').select2({

            placeholder: 'Search Payment To',

            minimumInputLength: 0,

            minimumResultsForSearch: 1,

            tags: false,

            multiple: false,

            tokenSeparators: [',', ' '],

            // templateResult: formatRepo,

            // templateSelection: formatOptions,

            allowClear: true,

            ajax: {

                url: "{{ route('backend.payments.searchVendor') }}",

                type: 'POST',

                cache: true,

                data: function(params) {

                    let from_account = $('.from_account').find(':selected').data('s_no');
                    let internal_external = $("#internal_external").val();

                    var query = {

                        search: params.term,

                        from_account: from_account,
                        internal_external: internal_external,

                        // type: 'public',

                        // page: params.page,

                        _token: '{{ csrf_token() }}'

                    }



                    // Query parameters will be ?search=[term]&type=public

                    return query;

                },



                processResults: function(data, params) {

                    // parse the results into the format expected by Select2

                    // since we are using custom formatting functions we do not need to

                    // alter the remote JSON data, except to indicate that infinite

                    // scrolling can be used

                    // params.page = params.page || 1;

                    if (data.success != undefined || data.success != null) {

                        // console.log("params", params)

                        // console.log("data", data.success)

                        if (data.success == false) {

                            $('.add-product').hide();

                            alert(data.message);

                        } else {

                            $('.add-product').show();

                            return {

                                results: $.map(data.data, function(item) {

                                    let project_name = null

                                    let benificiary_name = null

                                    let to_account_type = null

                                    // let project_name = item.project ? item.project+' '+item.short_name : item.project

                                    if (item.vendor_type == 'External') {

                                        project_name = item.account_number.substr(item.account_number

                                            .length - 4) + '-' + item.short_name

                                        benificiary_name = item.short_name;

                                        to_account_type = 'Ext';

                                    } else {

                                        project_name = item.project ? item.project + ' ' + item

                                            .short_name : item.project

                                        benificiary_name = item.project ? item.project + ' ' + item

                                            .short_name : item.project

                                        to_account_type = benificiary_name;

                                    }

                                    console.log(item.vendor_type)

                                    console.log(benificiary_name)



                                    // console.log("Last 4 Digit: ", item.account_number.substr(item.account_number.length - 4))

                                    return {

                                        text: project_name,

                                        id: project_name,

                                        data_id: item.id,

                                        s_no: item.s_no,

                                        account_number: item.account_number,

                                        vendor_name: item.vendor_name,

                                        vendor_nick_name: item.vendor_nick_name,

                                        benificiary_name: benificiary_name,

                                        from_account_type: item.from_account_type,

                                        name_of_bank: item.name_of_bank,

                                        to_account_type: to_account_type,

                                    }

                                }),

                                /* pagination: {

                                    more: (params.page * 1) < data.total

                                } */

                            };

                        }

                    }



                }

            },

            // I set `staff_constant` field here

            templateSelection: function(data, container) {

                console.log("data", data)

                console.log("container", container)

                // let project_name = data.project ? data.project+' '+data.short_name : data.project

                // let benificiary_name = data.project ? data.project+' '+data.short_name : data.project



                let project_name = null

                let benificiary_name = null

                let to_account_type = null

                // let project_name = item.project ? item.project+' '+item.short_name : item.project



                if (data.from_account_type == 'External') {

                    project_name = data.account_number.substr(data.account_number.length - 4) + '-' + data

                        .short_name

                    benificiary_name = data.short_name;

                    to_account_type = 'Ext';

                } else {

                    project_name = data.project ? data.project + ' ' + data.short_name : data.project

                    benificiary_name = data.project ? data.project + ' ' + data.short_name : data.project

                    to_account_type = benificiary_name;

                }

                console.log("data.vendor_type:", to_account_type)

                console.log("benificiary_name:", benificiary_name)

                $(data.element).attr('data-id', data.data_id);

                $(data.element).attr('data-ac', data.account_number);

                $(data.element).attr('data-name', data.vendor_name);

                $(data.element).attr('data-short_name', data.short_name);

                $(data.element).attr('data-project', project_name);

                $(data.element).attr('data-s_no', data.s_no);

                $(data.element).attr('data-vendor_nick_name', data.vendor_nick_name);

                $(data.element).attr('data-to_account_type', data.to_account_type);

                $(data.element).attr('data-benificiary', data.benificiary_name);

                // $(data.element).attr('data-from_account_type', data.from_account_type);

                $(data.element).attr('data-name-of-bank', data.name_of_bank);

                return data.text;

            },

        });





        $(document).on('change', '.from_account', function() {

            $('#from_account_no').val($(this).find(':selected').data('ac'));

            $('#from_account_type').val($(this).find(':selected').data('from_account_type'));

            $('#account_full_name').val($(this).find(':selected').data('name'));

        })

        $(document).on('click', '.request-queue-clear', function() {

            requestQueueClear()

        })

        $(document).on('change', '.vendor_code', function() {

            let i = $(this).data('index');

            let name = $(this).find(':selected').data('name');

            let ac = $(this).find(':selected').data('ac');

            let vendor_nick_name = $(this).find(':selected').data('vendor_nick_name');

            let benificiary_name = $(this).find(':selected').data('benificiary');

            let to_account_type = $(this).find(':selected').data('to_account_type');

            let name_of_bank = $(this).find(':selected').data('name-of-bank');



            $('#vendor_name').val(benificiary_name);

            $('#vendor_account').val(ac);

            $('#vendor_nick_name').val(vendor_nick_name);

            $('#benificiary_name').val(benificiary_name);

            $('#to_account_type').val(to_account_type);

            $('#name_of_bank').val(name_of_bank);

        })



        function addRequestInQueue() {

            var actionUrl = "{{ route('backend.payments.addRequestInQueue') }}";

            var formData = $('#requestFormCreate').serialize();

            $.ajax({

                url: actionUrl,

                type: 'POST',

                dataType: 'json',

                data: formData,

                // contentType: 'application/json',

                success: function(response) {

                    alert('Request added in queue');

                    $(".request-in-queue").html(response.html);

                    $(".btnRequestForm").addClass('show');

                    $(".btnRequestForm").removeClass('hide');

                },

                error: function(xhr, status, error) {

                    $(".btnRequestForm").addClass('hide');

                    $(".btnRequestForm").removeClass('show');

                    // console.log(error)

                    // iziToast.error({

                    //     message: 'An error occurred: ' + error,

                    //     position: 'topRight'

                    // });

                }

                // response: JSON.stringify(person)

            });

        }



        function deleteRequestInQueue(index) {

            var actionUrl = "{{ route('backend.payments.deleteRequestInQueue') }}";

            // var formData = $('#requestFormCreate').serialize();

            $.ajax({

                url: actionUrl,

                type: 'POST',

                dataType: 'json',

                data: {

                    index: index,

                    _token: '{{ csrf_token() }}'

                },

                // contentType: 'application/json',

                success: function(response) {

                    console.log(response)

                    $(".request-in-queue").html(response.html);

                },

                error: function(xhr, status, error) {



                    // console.log(error)

                    // iziToast.error({

                    //     message: 'An error occurred: ' + error,

                    //     position: 'topRight'

                    // });

                }

                // response: JSON.stringify(person)

            });

        }



        function requestQueueClear(index) {

            var actionUrl = "{{ route('backend.payments.deleteRequestInQueue') }}";

            // var formData = $('#requestFormCreate').serialize();

            $.ajax({

                url: actionUrl,

                type: 'POST',

                dataType: 'json',

                data: {

                    clearCart: true,

                    _token: '{{ csrf_token() }}'

                },

                // contentType: 'application/json',

                success: function(response) {

                    alert('Request queue empty, Add new request');

                    $(".request-in-queue").html(response.html);

                    $(".btnRequestForm").removeClass('show');

                    $(".btnRequestForm").addClass('hide');

                },

                error: function(xhr, status, error) {

                    alert('Unable to empty request queue');

                    $(".btnRequestForm").addClass('show');

                    $(".btnRequestForm").removeClass('hide');

                    // console.log(error)

                    // iziToast.error({

                    //     message: 'An error occurred: ' + error,

                    //     position: 'topRight'

                    // });

                }

                // response: JSON.stringify(person)

            });

        }

        /*  $(document).ready(function() {
             $('#from_ac').fSelect();
             $('#to_ac').fSelect();
         }); */
        $(function() {

            var templateType = $("#template_type");
            var internal_external = $("#internal_external");

            var project = $("#project");

            items = $(".request-in-queue");

            $(".add-product").on("click", function() {



                if (templateType.val() == '') {

                    alert('Please select template type');

                    return false

                } else if (project.val() == '') {

                    alert('Please select project');

                    return false

                } else if (internal_external.val() == '') {

                    alert('Please select Internal/External');

                    return false

                }



                addRequestInQueue();

            });



            $(document).on("click", ".delete-item", function() {

                deleteRequestInQueue($(this).data('index'))

                /* items

                    .find(".delete-item")

                    .eq($(this).index())

                    .remove(); */

            });

        });





        $(document).on('change', '.template_type', function() {

            var actionUrl = "{{ route('backend.payments.getFromAccount', ':id') }}";

            actionUrl = actionUrl.replace(':id', $(this).val());

            if ($(this).val() == '') {

                return false;

            }

            $.ajax({

                url: actionUrl,

                type: "GET",

                data: {},

                dataType: "JSON",

                processData: false,

                contentType: false,



                success: function(response) {

                    console.log(response)

                    /* if (response.errors) {

                        errorMsg = '';

                        $.each(response.errors, function(field, errors) {

                            $.each(errors, function(index, error) {

                                errorMsg += error + '<br>';

                            });

                        });

                        iziToast.error({

                            message: errorMsg,

                            position: 'topRight'

                        });



                    } else {

                        iziToast.success({

                            message: response.success,

                            position: 'topRight'



                        });

                    } */



                    $('.from_account').html(response.html)



                },

                error: function(xhr, status, error) {



                    alert('An error occurred: ' + error);

                }



            });



        })
    </script>

    <style>
        .tablecontainer {

            width: 100%;

            margin: 20px auto;

            border-radius: 10px;

            overflow: auto;

            /*box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);*/

            background-color: #fff;

        }



        .table-header {

            display: flex;

            align-items: center;

            padding: 10px;

            background-color: #f5f5f5;

            border-bottom: 1px solid #ddd;

        }



        .search-wrapper {

            flex: 1;

            display: flex;

            align-items: center;

        }



        .search-wrapper input {

            padding: 5px;

            border: 1px solid #ddd;

            border-radius: 5px;

            font-size: 16px;

            margin-right: 10px;

        }



        #requestFormtable {

            width: 100%;

            border-collapse: collapse;

        }



        #requestFormtable th,

        #requestFormtable td {

            padding: 12px;

            text-align: left;

            border-bottom: 1px solid #ddd;

        }



        #requestFormtable th {

            background-color: #f0f0f0;

            cursor: pointer;

            position: relative;

        }



        #requestFormtable th i {

            margin-left: 5px;

        }



        #requestFormtable td {

            max-width: 300px;

            /* Increased maximum width for better visibility of long addresses */

            overflow: hidden;

            text-overflow: ellipsis;

            white-space: nowrap;

        }



        #requestFormtable td img {

            width: 50px;

            /* Square width */

            height: 50px;

            /* Square height */

            border-radius: 5px;

            /* Square corners */

            display: block;

            margin: 0 auto;

            /* Centering the image */

        }
    </style>
@endpush
