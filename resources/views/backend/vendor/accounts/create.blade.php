@extends('backend.layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>Add Bank Account - {{ $vendor->vendor_name }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('backend.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('backend.vendor.index') }}">Vendors</a></li>
                <li class="breadcrumb-item"><a href="{{ route('backend.vendor.show', $vendor) }}">{{ $vendor->vendor_name }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('backend.vendor.accounts.index', $vendor) }}">Accounts</a></li>
                <li class="breadcrumb-item active">Add Account</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add New Bank Account</h5>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('backend.vendor.accounts.store', $vendor) }}" method="POST" class="row g-3">
                            @csrf

                            <div class="col-md-6">
                                <label for="account_name" class="form-label">Account Holder Name *</label>
                                <input type="text" class="form-control" id="account_name" name="account_name" 
                                       value="{{ old('account_name', $vendor->vendor_name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="account_number" class="form-label">Account Number *</label>
                                <input type="text" class="form-control" id="account_number" name="account_number" 
                                       value="{{ old('account_number') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="account_type" class="form-label">Account Type</label>
                                <select class="form-select" id="account_type" name="account_type">
                                    <option value="">Select Account Type</option>
                                    <option value="Savings" {{ old('account_type') == 'Savings' ? 'selected' : '' }}>Savings</option>
                                    <option value="Current" {{ old('account_type') == 'Current' ? 'selected' : '' }}>Current</option>
                                    <option value="Overdraft" {{ old('account_type') == 'Overdraft' ? 'selected' : '' }}>Overdraft</option>
                                    <option value="Cash Credit" {{ old('account_type') == 'Cash Credit' ? 'selected' : '' }}>Cash Credit</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="name_of_bank" class="form-label">Bank Name *</label>
                                <input type="text" class="form-control" id="name_of_bank" name="name_of_bank" 
                                       value="{{ old('name_of_bank') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="branch_name" class="form-label">Branch Name</label>
                                <input type="text" class="form-control" id="branch_name" name="branch_name" 
                                       value="{{ old('branch_name') }}">
                            </div>

                            <div class="col-md-6">
                                <label for="ifsc_code" class="form-label">IFSC Code *</label>
                                <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" 
                                       value="{{ old('ifsc_code') }}" required pattern="[A-Z]{4}0[A-Z0-9]{6}"
                                       title="Please enter a valid IFSC code (e.g., SBIN0001234)">
                            </div>

                            <div class="col-md-6">
                                <label for="swift_code" class="form-label">SWIFT Code</label>
                                <input type="text" class="form-control" id="swift_code" name="swift_code" 
                                       value="{{ old('swift_code') }}">
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary" value="1"
                                           {{ old('is_primary') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_primary">
                                        Set as Primary Account
                                    </label>
                                    <div class="form-text">Primary account will be used by default for payments.</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="3">{{ old('remarks') }}</textarea>
                            </div>

                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary">Create Account</button>
                                <a href="{{ route('backend.vendor.accounts.index', $vendor) }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Vendor Details</h5>
                        <div class="mb-2">
                            <strong>Vendor Code:</strong><br>
                            {{ $vendor->vendor_code }}
                        </div>
                        <div class="mb-2">
                            <strong>Vendor Name:</strong><br>
                            {{ $vendor->vendor_name }}
                        </div>
                        <div class="mb-2">
                            <strong>Email:</strong><br>
                            {{ $vendor->vendor_email ?? 'N/A' }}
                        </div>
                        <div class="mb-2">
                            <strong>Mobile:</strong><br>
                            {{ $vendor->vendor_mobile ?? 'N/A' }}
                        </div>
                        <div class="mb-2">
                            <strong>GSTIN:</strong><br>
                            {{ $vendor->gstin ?? 'N/A' }}
                        </div>
                        <div class="mb-2">
                            <strong>PAN:</strong><br>
                            {{ $vendor->pan ?? 'N/A' }}
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Existing Accounts</h5>
                        @if($vendor->accounts->count() > 0)
                            @foreach($vendor->accounts as $account)
                                <div class="mb-2 p-2 border rounded {{ $account->is_primary ? 'border-primary' : '' }}">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $account->account_number }}</strong>
                                        @if($account->is_primary)
                                            <span class="badge bg-primary">Primary</span>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $account->name_of_bank }}</small>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No existing accounts</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // IFSC Code validation and formatting
    const ifscInput = document.getElementById('ifsc_code');
    ifscInput.addEventListener('input', function(e) {
        // Convert to uppercase
        e.target.value = e.target.value.toUpperCase();
        
        // Remove any non-alphanumeric characters
        e.target.value = e.target.value.replace(/[^A-Z0-9]/g, '');
        
        // Limit to 11 characters
        if (e.target.value.length > 11) {
            e.target.value = e.target.value.substring(0, 11);
        }
    });
    
    // Account number validation (remove spaces and special characters)
    const accountInput = document.getElementById('account_number');
    accountInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });
});
</script>
@endpush
