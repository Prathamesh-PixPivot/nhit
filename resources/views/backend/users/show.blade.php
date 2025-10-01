@extends('backend.layouts.app')

@section('title', 'User Details - ' . $user->name)

@section('content')
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="bi bi-person-circle me-2"></i>User Details
                    </h2>
                    <p class="text-muted mb-0">View and manage user information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('backend.users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Users
                    </a>
                    @can('edit-user')
                        <a href="{{ route('backend.users.edit', $user->id) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-1"></i>Edit User
                        </a>
                    @endcan
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
                        <a href="{{ route('backend.users.index') }}">Users</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $user->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- User Information Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person text-primary me-2"></i>Basic Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Full Name:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $user->name }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Employee ID:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $user->emp_id ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Username:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $user->username ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Contact Number:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $user->number ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Email Address:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $user->email }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Status:</strong>
                                </div>
                                <div class="col-sm-8">
                                    @if($user->active === 'Y')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Organizational Information Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-building text-primary me-2"></i>Organizational Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Designation:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ @$user->designation->name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Department:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ @$user->department->name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Roles & Permissions Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-check text-primary me-2"></i>Roles & Permissions
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-12">
                            <strong>Assigned Roles:</strong>
                            <div class="mt-2">
                                @forelse ($user->roles as $role)
                                    <span class="badge bg-primary me-2">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted">No roles assigned</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banking Information Section -->
            @if($user->account_holder || $user->bank_name || $user->bank_account || $user->ifsc_code)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-credit-card text-primary me-2"></i>Banking Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Account Holder:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $user->account_holder ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Bank Name:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $user->bank_name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Account Number:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $user->bank_account ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>IFSC Code:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $user->ifsc_code ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Signature Section -->
            @if($user->file)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pen text-primary me-2"></i>Signature
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="text-center">
                                <img src="{{ asset('uploads/' . $user->file) }}" alt="User Signature"
                                     class="img-fluid border rounded" style="max-height: 200px; cursor: pointer;"
                                     data-bs-toggle="modal" data-bs-target="#signatureModal">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Metadata Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>Account Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Created:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $user->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y, h:i A') }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Last Updated:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $user->updated_at->setTimezone('Asia/Kolkata')->format('d/m/Y, h:i A') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('backend.users.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Users
                        </a>
                        @can('edit-user')
                            <a href="{{ route('backend.users.edit', $user->id) }}" class="btn btn-primary">
                                <i class="bi bi-pencil me-1"></i>Edit User
                            </a>
                        @endcan
                        @can('delete-user')
                            <form action="{{ route('backend.users.destroy', $user->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash me-1"></i>Delete User
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Signature Modal -->
    @if($user->file)
    <div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="signatureModalLabel">User Signature</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ asset('uploads/' . $user->file) }}" alt="User Signature" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }

    .badge {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
    }

    .row .col-sm-4 {
        font-weight: 600;
        color: #495057;
    }

    .row .col-sm-8 {
        color: #212529;
    }

    .img-fluid {
        max-width: 100%;
        height: auto;
    }

    .border {
        border: 1px solid #dee2e6 !important;
    }

    .rounded {
        border-radius: 0.375rem !important;
    }

    /* Modal styling */
    .modal-content {
        border-radius: 0.5rem;
        border: none;
    }

    .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        border-radius: 0.5rem 0.5rem 0 0;
    }
</style>
@endpush
