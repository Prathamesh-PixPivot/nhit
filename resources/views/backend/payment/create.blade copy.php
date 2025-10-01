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
                        <h5 class="card-title">Create Payment Request</h5>
                        <p>This is an examle page with no contrnt. You can use it as a starter for your custom pages.</p>
                        <form class="row g-3" action="{{ route('backend.payments.store') }}" method="post" id="requestForm"
                            name="requestForm">
                            @csrf

                            <div class="border p-3 d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary m-2 add-new-js"> Add New </button> <button
                                    type="submit"
                                    class="btn btn-primary m-2 offset-md-0 btn btn-primary request-form-submit"> Save
                                </button>
                            </div>
                            <div class="col-md-12">
                                <label for="template_type" class="form-label">Template Type</label>
                                <div class="col-md-12">
                                    <select name="template_type" id="template_type"
                                        class="form-control template_type @error('template_type') is-invalid @enderror">
                                        <option value="">--Select---</option>
                                        <option value="bulk-rtgs" {{ old('template_type') == 'mf-rtgs' ? 'selected' : '' }}>
                                            Bulk RTGS</option>
                                        <option value="mf-axis" {{ old('template_type') == 'mf-axis' ? 'selected' : '' }}>MF
                                            Axis</option>
                                        <option value="mf-kotak" {{ old('template_type') == 'mf-kotak' ? 'selected' : '' }}>
                                            MF Kotak</option>
                                        <option value="mf-sbi" {{ old('template_type') == 'mf-sbi' ? 'selected' : '' }}>MF
                                            SBI</option>
                                        <option value="rtgs" {{ old('template_type') == 'rtgs' ? 'selected' : '' }}>RTGS
                                        </option>
                                        <option value="salary" {{ old('template_type') == 'salary' ? 'selected' : '' }}>
                                            Salary</option>
                                        <option value="sbi" {{ old('template_type') == 'sbi' ? 'selected' : '' }}>SBI
                                        </option>
                                    </select>
                                    @error('template_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div id="example1">
                                <div class="r-group group border p-3 mt-3 section-group-js" data-index="0">
                                    <div class="d-flex justify-content-between align-items-center py-2 action-js">
                                        <p class="m-0 text-black-50">Request Details : #<span class="sn-js">1</span></p>

                                    </div>
                                    <div class="col-md-6">
                                        <label for="vendor_0_from_account" class="form-label" data-pattern-text="">Payment From</label>
                                        <div class="col-md-12">
                                            <select id="from_account_0"
                                                class="form-control from_account @error('from_account') is-invalid @enderror"
                                                name="vendor[0][from_account]" data-index="0">
                                            </select>
                                            @error('from_account')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="vendor_0_from_account_no" class="form-label">From Account A/C
                                            No.</label>
                                        <div class="col-md-12">
                                            <input type="text"
                                                class="form-control @error('from_account_no') is-invalid @enderror"
                                                id="from_account_no_0" value="{{ old('from_account_no') ?? '' }}"
                                                name="vendor[0][from_account_no]" data-index="0" readonly>
                                            @error('from_account_no')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="vendor_0_vendor_code" class="form-label">Payment To</label>
                                        <div class="col-md-12">
                                            @php
                                                $vendors = $helper->getAllVendors();
                                            @endphp
                                            <select id="vendor_code_0"
                                                class="form-control vendor_code @error('vendor_code') is-invalid @enderror"
                                                name="vendor[0][vendor_code]" data-index="0">
                                                <option value="">--Select---</option>
                                                @foreach ($vendors as $vendor)
                                                    <option value="{{ $vendor->vendor_code }}"
                                                        {{ old('vendor_code') == 'mf-rtgs' ? 'selected' : '' }}
                                                        data-name="{{ $vendor->vendor_name }}"
                                                        data-ac="{{ $vendor->account_number }}"
                                                        data-vendor_nick_name="{{ $vendor->vendor_nick_name }}"
                                                        data-benificiary="{{ $vendor->benificiary_name }}"
                                                        data-from_account_type="{{ $vendor->from_account_type }}">
                                                        {{ $vendor->vendor_code }}</option>
                                                @endforeach
                                            </select>
                                            @error('vendor_code')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="vendor_0_vendor_name" class="form-label">Name</label>
                                        <div class="col-md-12">
                                            <input type="text"
                                                class="form-control @error('vendor_name') is-invalid @enderror"
                                                id="vendor_name_0" value="{{ old('vendor_name') ?? '' }}"
                                                name="vendor[0][vendor_name]" readonly>
                                            @error('vendor_name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="vendor_0_vendor_account" class="form-label">A/C No.</label>
                                        <div class="col-md-12">
                                            <input type="text"
                                                class="form-control @error('vendor_account') is-invalid @enderror"
                                                id="vendor_account_0" value="{{ old('vendor_account') ?? '' }}"
                                                name="vendor[0][vendor_account]" readonly>
                                            @error('vendor_account')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="vendor_0_benificiary_name" class="form-label">Benificiary Name</label>
                                        <div class="col-md-12">
                                            <input type="text"
                                                class="form-control @error('benificiary_name') is-invalid @enderror"
                                                id="benificiary_name_0" value="{{ old('benificiary_name') ?? '' }}"
                                                name="vendor[0][benificiary_name]" readonly>
                                            @error('benificiary_name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="vendor_0_from_account_type" class="form-label">Account Type</label>
                                        <div class="col-md-12">
                                            <input type="text"
                                                class="form-control @error('from_account_type') is-invalid @enderror"
                                                id="from_account_type_0" value="{{ old('from_account_type') ?? '' }}"
                                                name="vendor[0][from_account_type]" readonly>
                                            @error('from_account_type')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="vendor_0_amount" class="form-label">Amount</label>
                                        <div class="col-md-12">
                                            <input type="number"
                                                class="form-control @error('amount') is-invalid @enderror" id="amount_0"
                                                value="{{ old('amount') ?? '' }}" name="vendor[0][amount]">
                                            @error('amount')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="vendor_0_purpose" class="form-label">Purpose</label>
                                        <div class="col-md-12">
                                            <textarea name="vendor[0][purpose]" id="" class="form-control @error('purpose') is-invalid @enderror" id="purpose_0" cols="30" rows="10">{{ old('purpose') ?? '' }}</textarea>
                                            @error('purpose')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-md-12 mt-5">
                                <input type="submit" class="col-md-2 offset-md-0 btn btn-primary request-form-submit"
                                    value="Save">
                            </div> --}}

                        </form>
                    </div>

                </div>

            </div>
    </section>
    {{-- <div class="container">
        <form class="form-floating mt-5" method="post" action="#">
            <div class="border p-3 d-flex justify-content-end">
                <button type="button" class="btn btn-secondary m-2 add-new-js"> Add New </button> <button type="submit" class="btn btn-primary m-2"> Save </button>
            </div>
            <div class="group border p-3 section-group-js" data-index="0">
                <div class="d-flex justify-content-between align-items-center py-2 action-js">
                    <p class="m-0 text-black-50">Person Details : #<span class="sn-js">1</span></p> 
                    
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="details[0][name]" class="form-control" id="name_0" placeholder="Enter your name">
                    <label> Name </label>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" name="details[0][email]" class="form-control" id="email_0" placeholder="name@example.com">
                    <label>Email address</label>
                </div>
            </div>
            
        </form>
    </div> --}}
@endsection
@push('script')
    <script>
        $(document).on('click', '.add-new-js', function() {

            var form = $(this).closest('form');
            var section = form.find('.section-group-js:last-child');

            var index = section.data('index');
            var newIndex = index + 1;
            var newSection = section.clone();
            console.log(index, typeof index);

            newSection.find('input').val('');
            newSection.data('index', newIndex);
            newSection.attr('data-index', newIndex);
            newSection.find('.sn-js').text(newIndex + 1);

            if (newSection.find('.remove-js').length === 0) {
                newSection.find('.action-js').append(`
               <button type="button" class="btn btn-danger remove-js"> <i class="fa fa-trash"></i> Delete</button>
               `);
            }

            updateAttributes(newSection, 'from_account', index);
            updateAttributes(newSection, 'from_account_no', index);
            updateAttributes(newSection, 'vendor_code', index);
            updateAttributes(newSection, 'vendor_name', index);
            updateAttributes(newSection, 'vendor_account', index);
            updateAttributes(newSection, 'amount', index);
            updateAttributes(newSection, 'vendor_nick_name', index);
            updateAttributes(newSection, 'benificiary_name', index);
            updateAttributes(newSection, 'from_account_type', index);
            updateAttributes(newSection, 'purpose', index);

            newSection.insertAfter(section);

        })

        $(document).on('click', '.remove-js', function() {
            var section = $(this).closest('.section-group-js');
            section.remove();
        });

        function updateAttributes(newSection, key, index) {
            var section = newSection.find(`[name="vendor[${index}][${key}]"]`);
            section.attr('name', `vendor[${index+1}][${key}]`);
            section.attr('id', `${key}_${index+1}`);
            section.attr('data-index', `${index+1}`);
        }

        $(document).on('change', '.from_account', function() {
            let i = $(this).data('index');
            $('#from_account_no_' + i).val($(this).find(':selected').data('ac'));
        })

        $(document).on('change', '.vendor_code', function() {
            let i = $(this).data('index');
            let name = $(this).find(':selected').data('name');
            let ac = $(this).find(':selected').data('ac');
            let vendor_nick_name = $(this).find(':selected').data('vendor_nick_name');
            let benificiary_name = $(this).find(':selected').data('benificiary');
            let from_account_type = $(this).find(':selected').data('from_account_type');
            $('#vendor_name_' + i).val(name);
            $('#vendor_account_' + i).val(ac);
            $('#vendor_nick_name_' + i).val(vendor_nick_name);
            $('#benificiary_name_' + i).val(benificiary_name);
            $('#from_account_type_' + i).val(from_account_type);
        })

        var errorMsg = '';
        error = false;;
        $('.select2').select2();

        // Wait for the DOM to be ready
        $(function() {
            
            // Initialize form validation on the registration form.
            // It has the name attribute "registration"
            $("form[name='requestForm']").validate({
                // Specify validation rules
                rules: {
                    // The key name on the left side is the name attribute
                    // of an input field. Validation rules are defined
                    // on the right side
                    template_type: "required",
                    "vendor_code[]": {required: true},
                    // "from_account[]":  {required: true},
                    // "from_account_no[]":  {required: true},
                    /* email: {
                        required: true,
                        // Specify that email should be validated
                        // by the built-in "email" rule
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 5
                    } */
                },
                // Specify validation error messages
                messages: {
                    template_type: "Please select template first",
                    lastname: "Please enter your lastname",
                    password: {
                        required: "Please provide a password",
                        minlength: "Your password must be at least 5 characters long"
                    },
                    email: "Please enter a valid email address"
                },
                // Make sure the form is submitted to the destination defined
                // in the "action" attribute of the form when valid
                submitHandler: function(form) {
                    form.submit();
                }
            });
        });
        /* $(document).on('change', '.template_type', function() {
            
        }) */


        $(document).on('change', '.template_type', function() {
            var actionUrl = "{{ route('backend.payments.getFromAccount', ':id') }}";
            actionUrl = actionUrl.replace(':id', $(this).val());
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

                    console.log(error)
                    iziToast.error({
                        message: 'An error occurred: ' + error,
                        position: 'topRight'
                    });
                }

            });

        })
        // $(".request-form-submit").click(function(e) {
        //     e.preventDefault();
        //     if ($('.template_type').val() == '') {
        //         errorMsg = "Please select template type";
        //         error = true;
        //         $('.request-form').hide();
        //     }else {
        //         $('.request-form').show();
        //     }
        //     if($('.vendor_code').val() == ''){
        //         errorMsg = "Please select vendor";
        //         error = true;
        //     }

        //     if(error == true){
        //         iziToast.error({
        //             message: errorMsg,
        //             position: 'topRight'
        //         });
        //         return false;
        //     }


        //     let form = $('#requestForm')[0];
        //     let data = new FormData(form);

        //     $.ajax({
        //         url: "{{ route('backend.payments.store') }}",
        //         type: "POST",
        //         data: data,
        //         dataType: "JSON",
        //         processData: false,
        //         contentType: false,

        //         success: function(response) {

        //             if (response.errors) {
        //                 errorMsg = '';
        //                 $.each(response.errors, function(field, errors) {
        //                     $.each(errors, function(index, error) {
        //                         errorMsg += error + '<br>';
        //                     });
        //                 });
        //                 iziToast.error({
        //                     message: errorMsg,
        //                     position: 'topRight'
        //                 });

        //             } else {
        //                 iziToast.success({
        //                     message: response.success,
        //                     position: 'topRight'

        //                 });
        //             }

        //         },
        //         error: function(xhr, status, error) {

        //             console.log(error)
        //             iziToast.error({
        //                 message: 'An error occurred: ' + error,
        //                 position: 'topRight'
        //             });
        //         }

        //     });

        // })
    </script>
    <style>
        form .error {
            color: #ff0000;
        }
    </style>
@endpush
