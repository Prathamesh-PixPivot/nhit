@extends('backend.layouts.app')
@section('content')
    <div class="pagetitle">
        <h1>Vendors</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Edit</h5>
                        @if ($errors->any())
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                                <strong>Validation Error(s):</strong>
                                <ul class="list-disc pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form class="row g-3" action="{{ route('backend.vendors.update', $vendor->id) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="col-md-6">
                                <label for="name" class="form-label">From Account Type</label>
                                <div class="col-md-12">
                                    <select id="from_account_type" name="from_account_type"
                                        class="form-select @error('from_account_type') is-invalid @enderror"
                                        onchange="toggleProjectField()">
                                        <option value="Internal"
                                            {{ old('from_account_type', $vendor->from_account_type) == 'Internal' ? 'selected' : '' }}>
                                            Internal</option>
                                        <option value="External"
                                            {{ old('from_account_type', $vendor->from_account_type) == 'External' ? 'selected' : '' }}>
                                            External</option>
                                    </select>
                                    @error('from_account_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6" id="projectField">
                                <label for="name" class="form-label">Project</label>
                                <div class="col-md-12">
                                    {{-- <input type="text" class="form-control @error('project') is-invalid @enderror"
                                        id="project" name="project" value="{{ old('project', $vendor->project) ?? '' }}"> --}}
                                    <select class="form-select form-control" id="project" name="project">
                                        <option value="">Select Project</option>
                                        @foreach ($filteredItems as $item)
                                            <option value="{{ $item->project }}"
                                                {{ old('project', $vendor->project) == $item->project ? 'selected' : '' }}>
                                                {{ $item->project }}</option>
                                        @endforeach
                                    </select>
                                    @error('project')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Status</label>
                                <div class="col-md-12">
                                    <select id="status" name="status"
                                        class="form-select @error('status') is-invalid @enderror">
                                        <option value="Active"
                                            {{ old('status', $vendor->status) == 'Active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="InActive"
                                            {{ old('status', $vendor->status) == 'InActive' ? 'selected' : '' }}>InActive
                                        </option>
                                    </select>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="name" class="form-label">Vendor Name</label>

                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('vendor_name') is-invalid @enderror"
                                        id="vendor_name" name="vendor_name"
                                        value="{{ old('vendor_name', $vendor->vendor_name) ?? '' }}">
                                    @error('vendor_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Vendor Code</label>

                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('vendor_code') is-invalid @enderror"
                                        id="vendor_code" name="vendor_code"
                                        value="{{ old('vendor_code', $vendor->vendor_code) ?? '' }}">
                                    @error('vendor_code')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="name" class="form-label">Vendor Email</label>

                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('vendor_email') is-invalid @enderror"
                                        id="vendor_email" name="vendor_email"
                                        value="{{ old('vendor_email', $vendor->vendor_email) ?? '' }}">
                                    @error('vendor_email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Vendor Mobile</label>

                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('vendor_mobile') is-invalid @enderror"
                                        id="vendor_mobile" name="vendor_mobile"
                                        value="{{ old('vendor_mobile', $vendor->vendor_mobile) ?? '' }}">
                                    @error('vendor_mobile')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Country Name</label>

                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('country_name') is-invalid @enderror"
                                        id="country_name" name="country_name"
                                        value="{{ old('country_name', $vendor->country_name) ?? '' }}">
                                    @error('country_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">State Name</label>

                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('state_name') is-invalid @enderror"
                                        id="state_name" name="state_name"
                                        value="{{ old('state_name', $vendor->state_name) ?? '' }}">
                                    @error('state_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">PIN Code</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('pin') is-invalid @enderror"
                                        id="pin" name="pin" value="{{ old('pin', $vendor->pin) ?? '' }}">
                                    @error('pin')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <label for="name" class="form-label">Email</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $vendor->email) ?? '' }}">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Mobile</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('mobile') is-invalid @enderror"
                                        id="mobile" name="mobile"
                                        value="{{ old('mobile', $vendor->mobile) ?? '' }}">
                                    @error('mobile')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div> --}}
                            <div class="col-md-6">
                                <label for="name" class="form-label">Account Number</label>

                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('account_number') is-invalid @enderror"
                                        id="account_number" name="account_number"
                                        value="{{ old('account_number', $vendor->account_number) ?? '' }}">
                                    @error('account_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Ifsc</label>

                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('ifsc_code') is-invalid @enderror"
                                        id="ifsc_code" name="ifsc_code"
                                        value="{{ old('ifsc_code', $vendor->ifsc_code) ?? '' }}">
                                    @error('ifsc_code')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Name Of Bank</label>

                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('name_of_bank') is-invalid @enderror"
                                        id="name_of_bank" name="name_of_bank"
                                        value="{{ old('name_of_bank', $vendor->name_of_bank) ?? '' }}">
                                    @error('name_of_bank')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Beneficiary Name</label>

                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('benificiary_name') is-invalid @enderror"
                                        id="benificiary_name" name="benificiary_name"
                                        value="{{ old('benificiary_name', $vendor->benificiary_name) ?? '' }}">
                                    @error('benificiary_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Pan</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('pan') is-invalid @enderror"
                                        id="pan" name="pan" value="{{ old('pan', $vendor->pan) ?? '' }}">
                                    @error('pan')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Gstin</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('gstin') is-invalid @enderror"
                                        id="gstin" name="gstin" value="{{ old('gstin', $vendor->gstin) ?? '' }}">
                                    @error('gstin')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">MSME Classification</label>

                                <div class="col-md-12">
                                    <select id="msme_classification" name="msme_classification"
                                        class="form-select @error('msme_classification') is-invalid @enderror">
                                        <option value="Micro"
                                            {{ old('msme_classification', $vendor->msme_classification) == 'Micro' ? 'selected' : '' }}>
                                            Micro</option>
                                        <option value="Small"
                                            {{ old('msme_classification', $vendor->msme_classification) == 'Small' ? 'selected' : '' }}>
                                            Small</option>
                                        <option value="Medium"
                                            {{ old('msme_classification', $vendor->msme_classification) == 'Medium' ? 'selected' : '' }}>
                                            Medium</option>
                                        <option value="Non-MSME"
                                            {{ old('msme_classification', $vendor->msme_classification) == 'Non-MSME' ? 'selected' : '' }}>
                                            Non-MSME</option>
                                    </select>
                                    @error('msme_classification')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Activity Type</label>

                                <div class="col-md-12">
                                    <select id="activity_type" name="activity_type"
                                        class="form-select @error('activity_type') is-invalid @enderror">
                                        <option value="N/A"
                                            {{ old('activity_type', $vendor->activity_type) == 'N/A' ? 'selected' : '' }}>
                                            N/A</option>
                                        <option value="Service"
                                            {{ old('activity_type', $vendor->activity_type) == 'Service' ? 'selected' : '' }}>
                                            Service</option>
                                        <option value="Trader"
                                            {{ old('activity_type', $vendor->activity_type) == 'Trader' ? 'selected' : '' }}>
                                            Trader</option>
                                        <option value="Manufacture"
                                            {{ old('activity_type', $vendor->activity_type) == 'Manufacture' ? 'selected' : '' }}>
                                            Manufacture</option>
                                    </select>
                                    @error('activity_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">MSME Start Date</label>

                                <div class="col-md-12">
                                    <input type="date"
                                        class="form-control @error('msme_start_date') is-invalid @enderror"
                                        id="msme_start_date" name="msme_start_date"
                                        value="{{ old('msme_start_date', isset($vendor->msme_start_date) ? \Carbon\Carbon::parse($vendor->msme_start_date)->format('Y-m-d') : '') }}">
                                    @error('msme_start_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Section 206AB Verified</label>

                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('section_206AB_verified') is-invalid @enderror"
                                        id="section_206AB_verified" name="section_206AB_verified"
                                        value="{{ old('section_206AB_verified', $vendor->section_206AB_verified) ?? '' }}">
                                    @error('section_206AB_verified')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="name" class="form-label">Remarks Address</label>

                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('remarks_address') is-invalid @enderror"
                                        id="remarks_address" name="remarks_address"
                                        value="{{ old('remarks_address', $vendor->remarks_address) ?? '' }}">
                                    @error('remarks_address')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Account Name</label>

                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('account_name') is-invalid @enderror"
                                        id="account_name" name="account_name"
                                        value="{{ old('account_name', $vendor->account_name) ?? '' }}">
                                    @error('account_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Short Name</label>

                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('short_name') is-invalid @enderror"
                                        id="short_name" name="short_name"
                                        value="{{ old('short_name', $vendor->short_name) ?? '' }}">
                                    @error('short_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Parent</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('parent') is-invalid @enderror"
                                        id="parent" name="parent"
                                        value="{{ old('parent', $vendor->parent) ?? '' }}">
                                    @error('parent')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-md-6">
                                <label for="name" class="form-label">Vendor Type</label>

                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('vendor_type') is-invalid @enderror"
                                        id="vendor_type" name="vendor_type"
                                        value="{{ old('vendor_type', $vendor->vendor_type) ?? '' }}">
                                    @error('vendor_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="name" class="form-label">Vendor Nick Name</label>

                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('vendor_nick_name') is-invalid @enderror"
                                        id="vendor_nick_name" name="vendor_nick_name"
                                        value="{{ old('vendor_nick_name', $vendor->vendor_nick_name) ?? '' }}">
                                    @error('vendor_nick_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>



                            <div class="col-md-6">
                                <label for="name" class="form-label">City Name</label>

                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('city_name') is-invalid @enderror"
                                        id="city_name" name="city_name"
                                        value="{{ old('city_name', $vendor->city_name) ?? '' }}">
                                    @error('city_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">MSME</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('msme') is-invalid @enderror"
                                        id="msme" name="msme" value="{{ old('msme', $vendor->msme) ?? '' }}">
                                    @error('msme')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="name" class="form-label">MSME Registration Number</label>

                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('msme_registration_number') is-invalid @enderror"
                                        id="msme_registration_number" name="msme_registration_number"
                                        value="{{ old('msme_registration_number', $vendor->msme_registration_number) ?? '' }}">
                                    @error('msme_registration_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="name" class="form-label">MSME End Date</label>

                                <div class="col-md-12">
                                    <input type="date"
                                        class="form-control @error('msme_end_date') is-invalid @enderror"
                                        id="msme_end_date" name="msme_end_date"
                                        value="{{ old('msme_end_date', isset($vendor->msme_end_date) ? \Carbon\Carbon::parse($vendor->msme_end_date)->format('Y-m-d') : '') }}">
                                    @error('msme_end_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Material Nature</label>

                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('material_nature') is-invalid @enderror"
                                        id="material_nature" name="material_nature"
                                        value="{{ old('material_nature', $vendor->material_nature) ?? '' }}">
                                    @error('material_nature')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Gst Defaulted</label>

                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('gst_defaulted') is-invalid @enderror"
                                        id="gst_defaulted" name="gst_defaulted"
                                        value="{{ old('gst_defaulted', $vendor->gst_defaulted) ?? '' }}">
                                    @error('gst_defaulted')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="name" class="form-label">Common Bank Details</label>

                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('common_bank_details') is-invalid @enderror"
                                        id="common_bank_details" name="common_bank_details"
                                        value="{{ old('common_bank_details', $vendor->common_bank_details) ?? '' }}">
                                    @error('common_bank_details')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Income Tax Type</label>

                                <div class="col-md-12">
                                    <input type="text"
                                        class="form-control @error('income_tax_type') is-invalid @enderror"
                                        id="income_tax_type" name="income_tax_type"
                                        value="{{ old('income_tax_type', $vendor->income_tax_type) ?? '' }}">
                                    @error('income_tax_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>


                            <!-- File Upload Input -->
                            <label class="block mt-4 col-6">
                                <span class="text-gray-700">Update Files (Optional)</span>
                                <input type="file" name="file_path[]" multiple class="form-control mt-1 block w-full">
                            </label>

                            @if (auth()->user()->hasRole('Admin'))
                                <div class="col-6">
                                    <label for="active" class="form-label">Approve</label>
                                    <select class="form-select form-control" id="active" name="active">
                                        <option value="Y"
                                            {{ old('active', $vendor->active) == 'Y' ? 'selected' : '' }}>
                                            Yes
                                        </option>
                                        <option value="N"
                                            {{ old('active', $vendor->active) == 'N' ? 'selected' : '' }}>
                                            No</option>
                                    </select>
                                    @error('active')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @else
                                <input type="hidden" value="N" name="active">
                            @endif
                            @if ($vendor->file_path)
                                <div class="mt-4 w-25">
                                    <label class="block text-sm font-medium text-gray-700">Uploaded Files:</label>
                                    <ul>
                                        @foreach (json_decode($vendor->file_path, true) ?? [] as $file)
                                            <li>
                                                @if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $file))
                                                    <img src="{{ asset('storage/vendorFile/' . $file) }}"
                                                        alt="Uploaded Image" width="100px" height="100px">
                                                @else
                                                    <a href="{{ asset('storage/vendorFile/' . $file) }}" target="_blank"
                                                        class="text-blue-500">{{ $file }}</a>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif


                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Approve</button>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
    </section>
@endsection
@push('script')
    <script>
        function toggleProjectField() {
            var accountType = document.getElementById("from_account_type").value;
            var projectField = document.getElementById("projectField");

            if (accountType === "External") {
                projectField.style.display = "none"; // Hide project field
            } else {
                projectField.style.display = "block"; // Show project field
            }
        }

        // Page reload hone pe bhi condition check kare
        document.addEventListener("DOMContentLoaded", function() {
            toggleProjectField();
        });
    </script>
@endpush
