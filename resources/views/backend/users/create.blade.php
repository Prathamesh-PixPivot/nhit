@extends('backend.layouts.app')

@section('title', 'Create User')

@section('content')
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="bi bi-person-plus me-2"></i>Create User
                    </h2>
                    <p class="text-muted mb-0">Fill in the details to create a new user account</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('backend.users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Users
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
                        <a href="{{ route('backend.users.index') }}">Users</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form action="{{ route('backend.users.store') }}" method="post" enctype="multipart/form-data" class="modern-form">
                @csrf

                <!-- Basic Information Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person text-primary me-2"></i>Basic Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" required
                                           value="{{ old('name') }}"
                                           placeholder="Full Name">
                                    <label for="name">Full Name</label>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('emp_id') is-invalid @enderror"
                                           id="emp_id" name="emp_id" required
                                           value="{{ old('emp_id') }}"
                                           placeholder="Employee ID">
                                    <label for="emp_id">Employee ID</label>
                                    @error('emp_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                           id="username" name="username" required
                                           value="{{ old('username') }}"
                                           placeholder="Username">
                                    <label for="username">Username</label>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" required
                                           value="{{ old('email') }}"
                                           placeholder="Email Address">
                                    <label for="email">Email Address</label>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('number') is-invalid @enderror"
                                           id="number" name="number" required
                                           value="{{ old('number') }}"
                                           placeholder="Contact Number">
                                    <label for="number">Contact Number</label>
                                    @error('number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('active') is-invalid @enderror"
                                            id="active" name="active" required>
                                        <option value="Y" {{ old('active', 'Y') == 'Y' ? 'selected' : '' }}>Active</option>
                                        <option value="N" {{ old('active') == 'N' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <label for="active">Status</label>
                                    @error('active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                <div class="form-floating">
                                    <select class="form-select @error('designation_id') is-invalid @enderror"
                                            id="designation_id" name="designation_id" required>
                                        <option value="">Select Designation</option>
                                        @foreach ($designations as $designation)
                                            <option value="{{ $designation->id }}"
                                                    {{ old('designation_id') == $designation->id ? 'selected' : '' }}>
                                                {{ $designation->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="designation_id">Designation</label>
                                    @error('designation_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('department_id') is-invalid @enderror"
                                            id="department_id" name="department_id" required>
                                        <option value="">Select Department</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                    {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="department_id">Department</label>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Security Information Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-shield-lock text-primary me-2"></i>Security Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password" required
                                           placeholder="Password">
                                    <label for="password">Password</label>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                           id="password_confirmation" name="password_confirmation" required
                                           placeholder="Confirm Password">
                                    <label for="password_confirmation">Confirm Password</label>
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Roles Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-shield-check text-primary me-2"></i>Roles & Permissions
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating">
                                    <select class="form-select @error('roles') is-invalid @enderror select2"
                                            multiple aria-label="Roles" id="roles" name="roles[]">
                                        @forelse ($roles as $role)
                                            @if ($role != 'Super Admin')
                                                <option value="{{ $role }}"
                                                        {{ collect(old('roles'))->contains($role) ? 'selected' : '' }}>
                                                    {{ $role }}
                                                </option>
                                            @else
                                                @if (Auth::user()->hasRole('Super Admin'))
                                                    <option value="{{ $role }}"
                                                            {{ in_array($role, $userRoles ?? []) ? 'selected' : '' }}>
                                                        {{ $role }}
                                                    </option>
                                                @endif
                                            @endif
                                        @empty
                                            <option value="">No roles available</option>
                                        @endforelse
                                    </select>
                                    <label for="roles">Assign Roles</label>
                                    @error('roles')
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
                                    <input type="text" class="form-control @error('account_holder') is-invalid @enderror"
                                           id="account_holder" name="account_holder"
                                           value="{{ old('account_holder') }}"
                                           placeholder="Account Holder Name">
                                    <label for="account_holder">Account Holder Name</label>
                                    @error('account_holder')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                                           id="bank_name" name="bank_name"
                                           value="{{ old('bank_name') }}"
                                           placeholder="Bank Name">
                                    <label for="bank_name">Bank Name</label>
                                    @error('bank_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('bank_account') is-invalid @enderror"
                                           id="bank_account" name="bank_account"
                                           value="{{ old('bank_account') }}"
                                           placeholder="Bank Account Number">
                                    <label for="bank_account">Bank Account Number</label>
                                    @error('bank_account')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('ifsc_code') is-invalid @enderror"
                                           id="ifsc_code" name="ifsc_code"
                                           value="{{ old('ifsc_code') }}"
                                           placeholder="IFSC Code">
                                    <label for="ifsc_code">IFSC Code</label>
                                    @error('ifsc_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Signature Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-pen text-primary me-2"></i>Signature
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-8">
                                <label for="file" class="form-label">Upload Signature</label>
                                <input type="file" accept="image/*" class="form-control @error('file') is-invalid @enderror"
                                       id="file" name="file" accept=".png,.jpg,.jpeg">
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Upload signature image (PNG, JPG - Max 2MB)</small>
                            </div>
                            <div class="col-md-4">
                                <div class="img-thumbnail" id="preview-box" style="display: none; cursor: pointer;">
                                    <img id="file-preview" src="" alt="Signature Preview" width="100" height="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('backend.users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-plus me-1"></i>Create User
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
        $(document).ready(function() {
            // Initialize Select2 for enhanced dropdowns (matches greenNote pattern)
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: function() {
                    return $(this).data('placeholder') || 'Select an option';
                }
            });

            const fileInput = document.getElementById('file');
            const previewBox = document.getElementById('preview-box');
            const previewImage = document.getElementById('file-preview');

            // Handle file input change
            fileInput.addEventListener('change', function(event) {
                const file = event.target.files[0];

                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewBox.style.display = 'block';
                        previewImage.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    previewBox.style.display = 'none';
                    previewImage.src = '';
                    alert('Please select a valid image file.');
                }
            });
        });
    </script>
    <style>
        /* Ensure Select2 dropdown appears above form-floating labels */
        .form-floating .select2-container {
            z-index: 1056;
        }

        .form-floating .select2-selection {
            height: 58px !important;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            padding: 1rem 0.75rem 0 0.75rem;
        }

        .form-floating .select2-selection__rendered {
            padding-top: 0.5rem;
        }

        .form-floating .select2-selection__placeholder {
            color: #6c757d;
        }

        .form-floating .select2-selection__arrow {
            top: 50%;
            transform: translateY(-50%);
        }

        /* Fix for invalid state styling */
        .form-floating .select2-container.is-invalid .select2-selection {
            border-color: #dc3545;
            padding-right: calc(1.5em + 0.75rem);
        }
    </style>
@endpush
