@extends('backend.layouts.app')

@section('title', 'Reimbursement Note Approval Rules - ' . ($approvalFlow->vendor->project ?? 'N/A'))

@section('content')
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-success mb-1">
                        <i class="bi bi-diagram-3 me-2"></i>Reimbursement Note Approval Rules
                    </h2>
                    <p class="text-muted mb-0">Configure reimbursement note approval workflows</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('backend.reimbursement-note.rule') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Rules
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
                        <a href="{{ route('backend.reimbursement-note.rule') }}">Reimbursement Note Rules</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Rule Details</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Approval Rules Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle text-success me-2"></i>Rule Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Vendor:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $approvalFlow->vendor->project ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Department:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $approvalFlow->department->name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approval Steps Section -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-ol text-success me-2"></i>Approval Steps
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="approval-flow-container">
                        @foreach ($approvalFlow->approvalSteps as $index => $step)
                            <div class="approval-step mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="step-number bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                         style="width: 40px; height: 40px; font-weight: bold;">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="step-info flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0">{{ $index == 0 ? 'Initiator' : 'Approver' }}</h6>
                                        </div>
                                        <p class="text-muted mb-0">{{ $step->nextOnApprove->name ?? 'N/A' }}</p>
                                        <small class="text-muted">{{ $step->nextOnApprove->email ?? '' }}</small>
                                    </div>
                                </div>
                                @if (!$loop->last)
                                    <div class="step-connector text-center my-3">
                                        <i class="bi bi-arrow-down text-success"></i>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('backend.reimbursement-note.rule') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Rules
                        </a>
                        <a href="{{ route('backend.approval.edit', $approvalFlow->id) }}" class="btn btn-success">
                            <i class="bi bi-pencil me-1"></i>Edit Rules
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(253, 126, 20, 0.15) !important;
    }

    .approval-step {
        padding: 1rem;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        background-color: #f8f9fa;
        transition: all 0.2s ease;
    }

    .approval-step:hover {
        background-color: #fff3cd;
        border-color: #fd7e14;
    }

    .step-number {
        font-size: 1.1rem;
    }

    .step-connector {
        font-size: 1.5rem;
        color: #198754;
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
</style>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            // Add any specific JavaScript for reimbursement note rules if needed
        });
    </script>
@endpush
