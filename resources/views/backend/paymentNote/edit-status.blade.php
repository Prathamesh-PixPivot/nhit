@extends('backend.layouts.app')

@section('title', 'Edit Payment Note Status - ' . ($note->note_no ?? 'N/A'))

@section('content')
    <!-- Page Title -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="bi bi-pencil-square me-2"></i>Edit Payment Note Status
                    </h2>
                    <p class="text-muted mb-0">Update the status of payment note: {{ $note->note_no }}</p>
                </div>
                <div>
                    <a href="{{ route('backend.payment-note.show', $note) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Details
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
                        <a href="{{ route('backend.payment-note.index') }}">Payment Notes</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('backend.payment-note.show', $note) }}">{{ $note->note_no }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Status</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Permission Info -->
            @php
                $currentApproverId = $note->getCurrentPendingApprover();
                $currentApprover = $currentApproverId ? \App\Models\User::find($currentApproverId) : null;
                $isCurrentApprover = $note->isCurrentApprover(auth()->id());
                $isSuperAdmin = auth()->user()->hasRole('Super Admin');
            @endphp

            @if($isCurrentApprover)
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                        <div>
                            <h6 class="mb-1 fw-bold">You are the Current Approver</h6>
                            <p class="mb-0 small">You can update the status as you are responsible for the current approval step.</p>
                        </div>
                    </div>
                </div>
            @elseif($isSuperAdmin)
                <div class="alert alert-warning border-0 shadow-sm mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-shield-fill-check me-3 fs-4"></i>
                        <div>
                            <h6 class="mb-1 fw-bold">SuperAdmin Access</h6>
                            <p class="mb-0 small">You have administrative privileges to update this payment note status.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Status Edit Form -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-toggle-on text-primary me-2"></i>Update Status
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <strong><i class="bi bi-exclamation-triangle me-2"></i>Validation Error(s):</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('backend.payment-note.update', ['paymentNote' => $note]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Current Status Display -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">Current Status</label>
                                    <div class="fs-5">
                                        @if($note->status === 'D')
                                            <span class="badge bg-secondary">Draft</span>
                                        @elseif($note->status === 'P')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($note->status === 'A')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($note->status === 'R')
                                            <span class="badge bg-danger">Rejected</span>
                                        @elseif($note->status === 'S')
                                            <span class="badge bg-info">Submitted</span>
                                        @elseif($note->status === 'PD')
                                            <span class="badge bg-success">Paid</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $note->status }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">Note Number</label>
                                    <div class="fs-6 fw-bold">{{ $note->note_no }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- New Status Selection -->
                        <div class="mb-4">
                            <label for="status" class="form-label fw-semibold">
                                <i class="bi bi-toggle-on text-primary me-2"></i>New Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" id="status" class="form-select form-select-lg @error('status') is-invalid @enderror" required>
                                <option value="">-- Select Status --</option>
                                <option value="D" {{ old('status', $note->status) === 'D' ? 'selected' : '' }}>Draft</option>
                                <option value="P" {{ old('status', $note->status) === 'P' ? 'selected' : '' }}>Pending</option>
                                <option value="S" {{ old('status', $note->status) === 'S' ? 'selected' : '' }}>Submitted</option>
                                <option value="A" {{ old('status', $note->status) === 'A' ? 'selected' : '' }}>Approved</option>
                                <option value="R" {{ old('status', $note->status) === 'R' ? 'selected' : '' }}>Rejected</option>
                                <option value="PD" {{ old('status', $note->status) === 'PD' ? 'selected' : '' }}>Paid</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Select the new status for this payment note
                            </div>
                        </div>

                        <!-- Hidden fields to preserve other data -->
                        <input type="hidden" name="note_no" value="{{ $note->note_no }}">
                        <input type="hidden" name="subject" value="{{ $note->subject }}">
                        <input type="hidden" name="recommendation_of_payment" value="{{ $note->recommendation_of_payment }}">
                        <input type="hidden" name="net_payable_round_off" value="{{ $note->net_payable_round_off }}">
                        <input type="hidden" name="add_particulars" value="{{ json_encode($note->add_particulars) }}">
                        <input type="hidden" name="less_particulars" value="{{ json_encode($note->less_particulars) }}">
                        <input type="hidden" name="status_only_edit" value="1">

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('backend.payment-note.show', $note) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>Payment Note Details
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Note No:</td>
                            <td class="fw-bold">{{ $note->note_no }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Subject:</td>
                            <td>{{ $note->subject }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Amount:</td>
                            <td class="fw-bold">₹{{ number_format($note->net_payable_round_off ?? 0, 2) }}</td>
                        </tr>
                        @if($note->greenNote)
                        <tr>
                            <td class="text-muted">Green Note:</td>
                            <td>{{ $note->greenNote->order_no }}</td>
                        </tr>
                        @endif
                        @if($note->reimbursementNote)
                        <tr>
                            <td class="text-muted">Reimbursement:</td>
                            <td>{{ $note->reimbursementNote->note_no }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Status Guide -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-question-circle text-primary me-2"></i>Status Guide
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2"><span class="badge bg-secondary me-2">D</span> Draft - Work in progress</li>
                        <li class="mb-2"><span class="badge bg-warning text-dark me-2">P</span> Pending - Awaiting approval</li>
                        <li class="mb-2"><span class="badge bg-info me-2">S</span> Submitted - Sent for review</li>
                        <li class="mb-2"><span class="badge bg-success me-2">A</span> Approved - Accepted</li>
                        <li class="mb-2"><span class="badge bg-danger me-2">R</span> Rejected - Declined</li>
                        <li class="mb-0"><span class="badge bg-success me-2">PD</span> Paid - Payment completed</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
