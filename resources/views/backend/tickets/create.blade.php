@extends('backend.layouts.app')

@section('title', 'Create Ticket')

@section('content')
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="bi bi-ticket-detailed me-2"></i>Create Support Ticket
                    </h2>
                    <p class="text-muted mb-0">Fill in the details to create a new support ticket</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('backend.tickets.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Tickets
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
                        <a href="{{ route('backend.tickets.index') }}">Tickets</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form action="{{ route('backend.tickets.store') }}" method="post" enctype="multipart/form-data" class="modern-form">
                @csrf

                <!-- Ticket Information Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle text-primary me-2"></i>Ticket Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" required
                                           value="{{ old('name', auth()->user()->name) }}"
                                           placeholder="Your Name">
                                    <label for="name">Full Name</label>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('number') is-invalid @enderror"
                                           id="number" name="number" required
                                           value="{{ old('number', auth()->user()->number) }}"
                                           placeholder="Contact Number">
                                    <label for="number">Contact Number</label>
                                    @error('number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('error') is-invalid @enderror"
                                           id="error" name="error" required
                                           value="{{ old('error') }}"
                                           placeholder="Error Title">
                                    <label for="error">Error Title</label>
                                    @error('error')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('entity_name') is-invalid @enderror"
                                           id="entity_name" name="entity_name" required
                                           value="{{ old('entity_name', config('app.short_name')) }}"
                                           placeholder="Entity Name">
                                    <label for="entity_name">Entity Name</label>
                                    @error('entity_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('priority') is-invalid @enderror"
                                            id="priority" name="priority" required>
                                        <option value="L" {{ old('priority') == 'L' ? 'selected' : '' }}>Low</option>
                                        <option value="M" {{ old('priority', 'M') == 'M' ? 'selected' : '' }}>Medium</option>
                                        <option value="H" {{ old('priority') == 'H' ? 'selected' : '' }}>High</option>
                                    </select>
                                    <label for="priority">Priority</label>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-file-text text-primary me-2"></i>Description
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description"
                                              rows="6" placeholder="Describe your issue in detail">{{ old('description') }}</textarea>
                                    <label for="description">Description</label>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attachments Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-paperclip text-primary me-2"></i>Attachments
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12">
                                <label for="attachment" class="form-label">Upload Images/Videos (Optional)</label>
                                <input type="file" name="attachment[]" id="attachment" multiple
                                       accept="image/*,video/*" class="form-control @error('attachment') is-invalid @enderror">
                                @error('attachment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Upload relevant images or videos to help explain the issue</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('backend.tickets.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-ticket-detailed me-1"></i>Create Ticket
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
            // Add any specific JavaScript for ticket creation if needed
        });
    </script>
@endpush
