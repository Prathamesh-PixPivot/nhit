@extends('backend.layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>Vendor Accounts - {{ $vendor->vendor_name }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('backend.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('backend.vendor.index') }}">Vendors</a></li>
                <li class="breadcrumb-item"><a href="{{ route('backend.vendor.show', $vendor) }}">{{ $vendor->vendor_name }}</a></li>
                <li class="breadcrumb-item active">Accounts</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Bank Accounts</h5>
                            <a href="{{ route('backend.vendor.accounts.create', $vendor) }}" class="btn btn-primary">
                                <i class="bi bi-plus"></i> Add New Account
                            </a>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($accounts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Account Name</th>
                                            <th>Account Number</th>
                                            <th>Bank Name</th>
                                            <th>IFSC Code</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Primary</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($accounts as $account)
                                            <tr class="{{ $account->is_primary ? 'table-success' : '' }}">
                                                <td>{{ $account->account_name }}</td>
                                                <td>{{ $account->account_number }}</td>
                                                <td>{{ $account->name_of_bank }}</td>
                                                <td>{{ $account->ifsc_code }}</td>
                                                <td>{{ $account->account_type ?? 'N/A' }}</td>
                                                <td>
                                                    @if($account->is_active)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($account->is_primary)
                                                        <span class="badge bg-primary">Primary</span>
                                                    @else
                                                        <form action="{{ route('backend.vendor.accounts.set-primary', [$vendor, $account]) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                                Set Primary
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('backend.vendor.accounts.show', [$vendor, $account]) }}" 
                                                           class="btn btn-sm btn-info">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('backend.vendor.accounts.edit', [$vendor, $account]) }}" 
                                                           class="btn btn-sm btn-warning">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('backend.vendor.accounts.toggle-status', [$vendor, $account]) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm {{ $account->is_active ? 'btn-secondary' : 'btn-success' }}"
                                                                    title="{{ $account->is_active ? 'Deactivate' : 'Activate' }}">
                                                                <i class="bi bi-{{ $account->is_active ? 'pause' : 'play' }}"></i>
                                                            </button>
                                                        </form>
                                                        @if($accounts->count() > 1)
                                                            <form action="{{ route('backend.vendor.accounts.destroy', [$vendor, $account]) }}" 
                                                                  method="POST" class="d-inline" 
                                                                  onsubmit="return confirm('Are you sure you want to delete this account?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted">No bank accounts found for this vendor.</p>
                                <a href="{{ route('backend.vendor.accounts.create', $vendor) }}" class="btn btn-primary">
                                    Add First Account
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Vendor Information Card --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Vendor Information</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Vendor Code:</strong><br>
                                {{ $vendor->vendor_code }}
                                @if($vendor->code_auto_generated)
                                    <small class="text-muted">(Auto-generated)</small>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <strong>Vendor Name:</strong><br>
                                {{ $vendor->vendor_name }}
                            </div>
                            <div class="col-md-3">
                                <strong>Email:</strong><br>
                                {{ $vendor->vendor_email ?? 'N/A' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Mobile:</strong><br>
                                {{ $vendor->vendor_mobile ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <strong>GSTIN:</strong><br>
                                {{ $vendor->gstin ?? 'N/A' }}
                            </div>
                            <div class="col-md-3">
                                <strong>PAN:</strong><br>
                                {{ $vendor->pan ?? 'N/A' }}
                            </div>
                            <div class="col-md-3">
                                <strong>MSME Classification:</strong><br>
                                {{ $vendor->msme_classification ?? 'N/A' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Status:</strong><br>
                                @if($vendor->active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
<script>
// Auto-hide success/error messages after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>
@endpush
