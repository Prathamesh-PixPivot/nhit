@extends('backend.layouts.app')

@section('title', 'Create Vendor')

@section('content')
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="bi bi-building me-2"></i>Create Vendor
                    </h2>
                    <p class="text-muted mb-0">Fill in the details to create a new vendor</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('backend.vendors.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Vendors
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('backend.dashboard.index') }}">
                            <i class="bi bi-house-door me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('backend.vendors.index') }}">Vendors</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form action="{{ route('backend.vendors.store') }}" method="post" enctype="multipart/form-data" class="modern-form">
                @csrf

                <!-- Account Information Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle text-primary me-2"></i>Account Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('from_account_type') is-invalid @enderror"
                                            id="from_account_type" name="from_account_type" onchange="toggleProjectField()">
                                        <option value="Internal" {{ old('from_account_type') == 'Internal' ? 'selected' : '' }}>Internal</option>
                                        <option value="External" {{ old('from_account_type') == 'External' ? 'selected' : '' }}>External</option>
                                    </select>
                                    <label for="from_account_type">Account Type</label>
                                    @error('from_account_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6" id="projectField">
                                <div class="form-floating">
                                    <select class="form-select @error('project') is-invalid @enderror"
                                            id="project" name="project">
                                        <option value="">Select Project</option>
                                        @foreach ($filteredItems as $item)
                                            <option value="{{ $item->project }}"
                                                    {{ old('project') == $item->project ? 'selected' : '' }}>
                                                {{ $item->project }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="project">Project</label>
                                    @error('project')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('status') is-invalid @enderror"
                                            id="status" name="status">
                                        <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="InActive" {{ old('status') == 'InActive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <label for="status">Status</label>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vendor Information Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person-badge text-primary me-2"></i>Vendor Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('vendor_name') is-invalid @enderror"
                                           id="vendor_name" name="vendor_name" required
                                           value="{{ old('vendor_name') }}"
                                           placeholder="Vendor Name">
                                    <label for="vendor_name">Vendor Name</label>
                                    @error('vendor_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('vendor_code') is-invalid @enderror"
                                           id="vendor_code" name="vendor_code" required
                                           value="{{ old('vendor_code') }}"
                                           placeholder="Vendor Code">
                                    <label for="vendor_code">Vendor Code</label>
                                    @error('vendor_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control @error('vendor_email') is-invalid @enderror"
                                           id="vendor_email" name="vendor_email" required
                                           value="{{ old('vendor_email') }}"
                                           placeholder="Vendor Email">
                                    <label for="vendor_email">Vendor Email</label>
                                    @error('vendor_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('vendor_mobile') is-invalid @enderror"
                                           id="vendor_mobile" name="vendor_mobile"
                                           value="{{ old('vendor_mobile') }}"
                                           placeholder="Vendor Mobile">
                                    <label for="vendor_mobile">Vendor Mobile</label>
                                    @error('vendor_mobile')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Information Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-geo-alt text-primary me-2"></i>Address Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('country_name') is-invalid @enderror"
                                           id="country_name" name="country_name"
                                           value="{{ old('country_name') }}"
                                           placeholder="Country Name">
                                    <label for="country_name">Country Name</label>
                                    @error('country_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('state_name') is-invalid @enderror"
                                           id="state_name" name="state_name"
                                           value="{{ old('state_name') }}"
                                           placeholder="State Name">
                                    <label for="state_name">State Name</label>
                                    @error('state_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('city_name') is-invalid @enderror"
                                           id="city_name" name="city_name"
                                           value="{{ old('city_name') }}"
                                           placeholder="City Name">
                                    <label for="city_name">City Name</label>
                                    @error('city_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('pin') is-invalid @enderror"
                                           id="pin" name="pin"
                                           value="{{ old('pin') }}"
                                           placeholder="PIN Code">
                                    <label for="pin">PIN Code</label>
                                    @error('pin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Banking Information Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-credit-card text-primary me-2"></i>Banking Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('account_number') is-invalid @enderror"
                                           id="account_number" name="account_number" required
                                           value="{{ old('account_number') }}"
                                           placeholder="Account Number">
                                    <label for="account_number">Account Number</label>
                                    @error('account_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('ifsc_code') is-invalid @enderror"
                                           id="ifsc_code" name="ifsc_code" required
                                           value="{{ old('ifsc_code') }}"
                                           placeholder="IFSC Code">
                                    <label for="ifsc_code">IFSC Code</label>
                                    @error('ifsc_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('name_of_bank') is-invalid @enderror"
                                           id="name_of_bank" name="name_of_bank" required
                                           value="{{ old('name_of_bank') }}"
                                           placeholder="Bank Name">
                                    <label for="name_of_bank">Bank Name</label>
                                    @error('name_of_bank')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('benificiary_name') is-invalid @enderror"
                                           id="benificiary_name" name="benificiary_name" required
                                           value="{{ old('benificiary_name') }}"
                                           placeholder="Beneficiary Name">
                                    <label for="benificiary_name">Beneficiary Name</label>
                                    @error('benificiary_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('account_name') is-invalid @enderror"
                                           id="account_name" name="account_name"
                                           value="{{ old('account_name') }}"
                                           placeholder="Account Name">
                                    <label for="account_name">Account Name</label>
                                    @error('account_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tax Information Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-receipt text-primary me-2"></i>Tax Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('pan') is-invalid @enderror"
                                           id="pan" name="pan" required
                                           value="{{ old('pan') }}"
                                           placeholder="PAN">
                                    <label for="pan">PAN</label>
                                    @error('pan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('gstin') is-invalid @enderror"
                                           id="gstin" name="gstin"
                                           value="{{ old('gstin') }}"
                                           placeholder="GSTIN">
                                    <label for="gstin">GSTIN</label>
                                    @error('gstin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MSME Information Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-award text-primary me-2"></i>MSME Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('msme_classification') is-invalid @enderror"
                                            id="msme_classification" name="msme_classification">
                                        <option value="Micro" {{ old('msme_classification') == 'Micro' ? 'selected' : '' }}>Micro</option>
                                        <option value="Small" {{ old('msme_classification') == 'Small' ? 'selected' : '' }}>Small</option>
                                        <option value="Medium" {{ old('msme_classification') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="Non-MSME" {{ old('msme_classification') == 'Non-MSME' ? 'selected' : '' }}>Non-MSME</option>
                                    </select>
                                    <label for="msme_classification">MSME Classification</label>
                                    @error('msme_classification')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('activity_type') is-invalid @enderror"
                                            id="activity_type" name="activity_type">
                                        <option value="N/A" {{ old('activity_type', 'N/A') == 'N/A' ? 'selected' : '' }}>N/A</option>
                                        <option value="Service" {{ old('activity_type') == 'Service' ? 'selected' : '' }}>Service</option>
                                        <option value="Trader" {{ old('activity_type') == 'Trader' ? 'selected' : '' }}>Trader</option>
                                        <option value="Manufacture" {{ old('activity_type') == 'Manufacture' ? 'selected' : '' }}>Manufacture</option>
                                    </select>
                                    <label for="activity_type">Activity Type</label>
                                    @error('activity_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('msme_start_date') is-invalid @enderror"
                                           id="msme_start_date" name="msme_start_date"
                                           value="{{ old('msme_start_date') }}">
                                    <label for="msme_start_date">MSME Start Date</label>
                                    @error('msme_start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('msme_end_date') is-invalid @enderror"
                                           id="msme_end_date" name="msme_end_date"
                                           value="{{ old('msme_end_date') }}">
                                    <label for="msme_end_date">MSME End Date</label>
                                    @error('msme_end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('msme_registration_number') is-invalid @enderror"
                                           id="msme_registration_number" name="msme_registration_number"
                                           value="{{ old('msme_registration_number') }}"
                                           placeholder="MSME Registration Number">
                                    <label for="msme_registration_number">MSME Registration Number</label>
                                    @error('msme_registration_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('msme') is-invalid @enderror"
                                           id="msme" name="msme"
                                           value="{{ old('msme') }}"
                                           placeholder="MSME">
                                    <label for="msme">MSME</label>
                                    @error('msme')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-file-text text-primary me-2"></i>Additional Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('section_206AB_verified') is-invalid @enderror"
                                           id="section_206AB_verified" name="section_206AB_verified"
                                           value="{{ old('section_206AB_verified') }}"
                                           placeholder="Section 206AB Verified">
                                    <label for="section_206AB_verified">Section 206AB Verified</label>
                                    @error('section_206AB_verified')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('remarks_address') is-invalid @enderror"
                                           id="remarks_address" name="remarks_address"
                                           value="{{ old('remarks_address') }}"
                                           placeholder="Remarks Address">
                                    <label for="remarks_address">Remarks Address</label>
                                    @error('remarks_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('short_name') is-invalid @enderror"
                                           id="short_name" name="short_name"
                                           value="{{ old('short_name') }}"
                                           placeholder="Short Name">
                                    <label for="short_name">Short Name</label>
                                    @error('short_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('parent') is-invalid @enderror"
                                           id="parent" name="parent"
                                           value="{{ old('parent') }}"
                                           placeholder="Parent">
                                    <label for="parent">Parent</label>
                                    @error('parent')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('material_nature') is-invalid @enderror"
                                           id="material_nature" name="material_nature"
                                           value="{{ old('material_nature') }}"
                                           placeholder="Material Nature">
                                    <label for="material_nature">Material Nature</label>
                                    @error('material_nature')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('gst_defaulted') is-invalid @enderror"
                                           id="gst_defaulted" name="gst_defaulted"
                                           value="{{ old('gst_defaulted') }}"
                                           placeholder="GST Defaulted">
                                    <label for="gst_defaulted">GST Defaulted</label>
                                    @error('gst_defaulted')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('common_bank_details') is-invalid @enderror"
                                           id="common_bank_details" name="common_bank_details"
                                           value="{{ old('common_bank_details') }}"
                                           placeholder="Common Bank Details">
                                    <label for="common_bank_details">Common Bank Details</label>
                                    @error('common_bank_details')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('income_tax_type') is-invalid @enderror"
                                           id="income_tax_type" name="income_tax_type"
                                           value="{{ old('income_tax_type') }}"
                                           placeholder="Income Tax Type">
                                    <label for="income_tax_type">Income Tax Type</label>
                                    @error('income_tax_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-paperclip text-primary me-2"></i>Documents
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12">
                                <label for="file_path" class="form-label">Upload Documents</label>
                                <input type="file" class="form-control @error('file_path') is-invalid @enderror"
                                       id="file_path" name="file_path[]" multiple
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.csv">
                                @error('file_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Upload supporting documents (PDF, DOC, XLS - Max 5MB each)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('backend.vendors.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-building me-1"></i>Create Vendor
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function toggleProjectField() {
            var accountType = document.getElementById("from_account_type").value;
            var projectField = document.getElementById("projectField");

            if (accountType === "External") {
                projectField.style.display = "none";
            } else {
                projectField.style.display = "block";
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            toggleProjectField();
        });
    </script>
@endpush
