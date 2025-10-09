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
                                           placeholder="Vendor Code" readonly>
                                    <label for="vendor_code">Vendor Code (Auto-Generated)</label>
                                    @error('vendor_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mt-2">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="generateVendorCode">
                                            <i class="bi bi-arrow-clockwise me-1"></i>Generate Code
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="manualVendorCode">
                                            <i class="bi bi-pencil me-1"></i>Manual Entry
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Format: <strong>{TypePrefix}{NamePrefix}{Year}{Sequence}</strong>
                                    </small>
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
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-credit-card text-primary me-2"></i>Banking Information
                            </h5>
                            <button type="button" class="btn btn-sm btn-outline-success" id="addBankAccount">
                                <i class="bi bi-plus-circle me-1"></i>Add Another Account
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Instructions -->
                        <div class="alert alert-info border-0 mb-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle text-info me-3 fs-5"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Multiple Bank Accounts</h6>
                                    <p class="mb-0 small">You can add multiple bank accounts for this vendor. The first account will be marked as primary by default. IFSC codes will auto-populate bank details.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div id="bankAccountsContainer">
                            <!-- Primary Account -->
                            <div class="bank-account-row border rounded p-4 mb-4 bg-light" data-account-index="0">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="text-primary mb-0">
                                        <i class="bi bi-bank me-2"></i>Primary Bank Account
                                    </h6>
                                    <span class="badge bg-primary">Primary</span>
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('accounts.0.account_number') is-invalid @enderror"
                                                   id="account_number_0" name="accounts[0][account_number]" required
                                                   value="{{ old('accounts.0.account_number') }}"
                                                   placeholder="Account Number">
                                            <label for="account_number_0">Account Number *</label>
                                            @error('accounts.0.account_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control ifsc-field @error('accounts.0.ifsc_code') is-invalid @enderror"
                                                   id="ifsc_code_0" name="accounts[0][ifsc_code]" required
                                                   value="{{ old('accounts.0.ifsc_code') }}"
                                                   placeholder="IFSC Code" maxlength="11" style="text-transform: uppercase;">
                                            <label for="ifsc_code_0">IFSC Code *</label>
                                            @error('accounts.0.ifsc_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control bank-name-field @error('accounts.0.name_of_bank') is-invalid @enderror"
                                                   id="name_of_bank_0" name="accounts[0][name_of_bank]" required
                                                   value="{{ old('accounts.0.name_of_bank') }}"
                                                   placeholder="Bank Name">
                                            <label for="name_of_bank_0">Bank Name *</label>
                                            @error('accounts.0.name_of_bank')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control branch-name-field @error('accounts.0.branch_name') is-invalid @enderror"
                                                   id="branch_name_0" name="accounts[0][branch_name]"
                                                   value="{{ old('accounts.0.branch_name') }}"
                                                   placeholder="Branch Name">
                                            <label for="branch_name_0">Branch Name</label>
                                            @error('accounts.0.branch_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('accounts.0.beneficiary_name') is-invalid @enderror"
                                                   id="beneficiary_name_0" name="accounts[0][beneficiary_name]" required
                                                   value="{{ old('accounts.0.beneficiary_name') }}"
                                                   placeholder="Beneficiary Name">
                                            <label for="beneficiary_name_0">Beneficiary Name *</label>
                                            @error('accounts.0.beneficiary_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('accounts.0.account_name') is-invalid @enderror"
                                                   id="account_name_0" name="accounts[0][account_name]"
                                                   value="{{ old('accounts.0.account_name') }}"
                                                   placeholder="Account Name">
                                            <label for="account_name_0">Account Name (Optional)</label>
                                            @error('accounts.0.account_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="accounts[0][is_primary]" 
                                                   id="is_primary_0" value="1" checked disabled>
                                            <label class="form-check-label" for="is_primary_0">
                                                <strong>Primary Account</strong> - This will be the default account for payments
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <input type="hidden" name="accounts[0][is_primary]" value="1">
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
<script src="{{ asset('js/banking-details-helper.js') }}"></script>
<script>
    let accountIndex = 1;
    let bankingHelper;
    
    function toggleProjectField() {
        var accountType = document.getElementById("from_account_type").value;
        var projectField = document.getElementById("projectField");

        if (accountType === "External") {
            projectField.style.display = "none";
        } else {
            projectField.style.display = "block";
        }
    }

    // Auto-generate vendor code
    async function generateVendorCode() {
        const vendorName = document.getElementById('vendor_name').value;
        const accountType = document.getElementById('from_account_type').value;
        
        if (!vendorName.trim()) {
            alert('Please enter vendor name first');
            document.getElementById('vendor_name').focus();
            return;
        }
        
        try {
            const response = await fetch('/api/backend/vendor/generate-code', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    vendor_name: vendorName,
                    account_type: accountType
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                document.getElementById('vendor_code').value = data.vendor_code;
                showNotification('Vendor code generated successfully!', 'success');
            } else {
                throw new Error(data.message || 'Failed to generate vendor code');
            }
        } catch (error) {
            console.error('Error generating vendor code:', error);
            showNotification('Error generating vendor code: ' + error.message, 'error');
        }
    }
    
    // Toggle manual entry for vendor code
    function toggleManualVendorCode() {
        const vendorCodeField = document.getElementById('vendor_code');
        const generateBtn = document.getElementById('generateVendorCode');
        const manualBtn = document.getElementById('manualVendorCode');
        
        if (vendorCodeField.readOnly) {
            vendorCodeField.readOnly = false;
            vendorCodeField.focus();
            generateBtn.style.display = 'none';
            manualBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Auto Generate';
            manualBtn.classList.remove('btn-outline-secondary');
            manualBtn.classList.add('btn-outline-primary');
        } else {
            vendorCodeField.readOnly = true;
            generateBtn.style.display = 'inline-block';
            manualBtn.innerHTML = '<i class="bi bi-pencil me-1"></i>Manual Entry';
            manualBtn.classList.remove('btn-outline-primary');
            manualBtn.classList.add('btn-outline-secondary');
        }
    }
    
    // Add new bank account
    function addBankAccount() {
        const container = document.getElementById('bankAccountsContainer');
        const newAccountHtml = `
            <div class="bank-account-row border rounded p-4 mb-4 bg-light" data-account-index="${accountIndex}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-secondary mb-0">
                        <i class="bi bi-bank me-2"></i>Additional Bank Account #${accountIndex}
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-account" onclick="removeAccount(this)">
                        <i class="bi bi-trash me-1"></i>Remove
                    </button>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" 
                                   id="account_number_${accountIndex}" name="accounts[${accountIndex}][account_number]" required
                                   placeholder="Account Number">
                            <label for="account_number_${accountIndex}">Account Number *</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control ifsc-field" 
                                   id="ifsc_code_${accountIndex}" name="accounts[${accountIndex}][ifsc_code]" required
                                   placeholder="IFSC Code" maxlength="11" style="text-transform: uppercase;">
                            <label for="ifsc_code_${accountIndex}">IFSC Code *</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control bank-name-field" 
                                   id="name_of_bank_${accountIndex}" name="accounts[${accountIndex}][name_of_bank]" required
                                   placeholder="Bank Name">
                            <label for="name_of_bank_${accountIndex}">Bank Name *</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control branch-name-field" 
                                   id="branch_name_${accountIndex}" name="accounts[${accountIndex}][branch_name]"
                                   placeholder="Branch Name">
                            <label for="branch_name_${accountIndex}">Branch Name</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" 
                                   id="beneficiary_name_${accountIndex}" name="accounts[${accountIndex}][beneficiary_name]" required
                                   placeholder="Beneficiary Name">
                            <label for="beneficiary_name_${accountIndex}">Beneficiary Name *</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" 
                                   id="account_name_${accountIndex}" name="accounts[${accountIndex}][account_name]"
                                   placeholder="Account Name">
                            <label for="account_name_${accountIndex}">Account Name (Optional)</label>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input primary-account-radio" type="radio" name="primary_account" 
                                   id="is_primary_${accountIndex}" value="${accountIndex}">
                            <label class="form-check-label" for="is_primary_${accountIndex}">
                                <strong>Set as Primary Account</strong>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', newAccountHtml);
        
        // Setup IFSC auto-completion for the new account
        setupIFSCForAccount(accountIndex);
        
        accountIndex++;
        
        showNotification('New bank account added successfully!', 'success');
    }
    
    // Remove bank account
    function removeAccount(button) {
        if (confirm('Are you sure you want to remove this bank account?')) {
            button.closest('.bank-account-row').remove();
            showNotification('Bank account removed successfully!', 'info');
        }
    }
    
    // Setup IFSC auto-completion for a specific account
    function setupIFSCForAccount(index) {
        if (bankingHelper) {
            bankingHelper.setupIFSCAutoComplete(
                `#ifsc_code_${index}`,
                `#name_of_bank_${index}`,
                `#branch_name_${index}`
            );
        }
    }
    
    // Show notification
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
    
    // Auto-populate beneficiary name from vendor name
    function autoPopulateBeneficiaryName() {
        const vendorName = document.getElementById('vendor_name').value;
        const beneficiaryFields = document.querySelectorAll('[id^="beneficiary_name_"]');
        
        beneficiaryFields.forEach(field => {
            if (!field.value.trim()) {
                field.value = vendorName;
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Initialize
        toggleProjectField();
        
        // Initialize banking helper
        bankingHelper = new BankingDetailsHelper({
            baseUrl: '/api/backend',
            debug: true
        });
        
        // Setup IFSC auto-completion for primary account
        setupIFSCForAccount(0);
        
        // Event listeners
        document.getElementById('generateVendorCode').addEventListener('click', generateVendorCode);
        document.getElementById('manualVendorCode').addEventListener('click', toggleManualVendorCode);
        document.getElementById('addBankAccount').addEventListener('click', addBankAccount);
        
        // Auto-populate beneficiary name when vendor name changes
        document.getElementById('vendor_name').addEventListener('blur', autoPopulateBeneficiaryName);
        
        // Handle primary account radio buttons
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('primary-account-radio')) {
                // Update hidden fields for primary account
                document.querySelectorAll('[name$="[is_primary]"]').forEach(input => {
                    if (input.type === 'hidden') {
                        input.value = '0';
                    }
                });
                
                const selectedIndex = e.target.value;
                const hiddenInput = document.querySelector(`[name="accounts[${selectedIndex}][is_primary]"]`);
                if (hiddenInput && hiddenInput.type === 'hidden') {
                    hiddenInput.value = '1';
                }
            }
        });
        
        // Generate initial vendor code
        setTimeout(() => {
            if (document.getElementById('vendor_name').value.trim()) {
                generateVendorCode();
            }
        }, 500);
    });
</script>
@endpush
