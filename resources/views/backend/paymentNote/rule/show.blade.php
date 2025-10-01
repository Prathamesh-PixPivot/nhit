@extends('backend.layouts.app')

@section('title', 'Payment Note Approval Rules - ' . ($step->min_amount ?? 'N/A') . ' - ' . ($step->max_amount ?? 'N/A'))

@section('content')
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-success mb-1">
                        <i class="bi bi-diagram-3 me-2"></i>Payment Note Approval Rules
                    </h2>
                    <p class="text-muted mb-0">Configure payment note approval workflows</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('backend.payment-note.rule') }}" class="btn btn-outline-secondary">
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
                        <a href="{{ route('backend.payment-note.rule') }}">Payment Note Rules</a>
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
                                    <strong>Minimum Amount:</strong>
                                </div>
                                <div class="col-sm-8">
                                    ₹{{ number_format($step->min_amount ?? 0) }} Lakhs
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Maximum Amount:</strong>
                                </div>
                                <div class="col-sm-8">
                                    ₹{{ number_format($step->max_amount ?? 0) }} Lakhs
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approvers Section -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people text-success me-2"></i>Assigned Approvers
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="approvers-container">
                        @foreach ($step->approvers as $index => $approver)
                            <div class="approver-item mb-3 p-3 border rounded">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="approver-number bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 35px; height: 35px; font-weight: bold; font-size: 0.9rem;">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $approver->reviewer->name ?? 'N/A' }}</h6>
                                            <small class="text-muted">{{ $approver->reviewer->email ?? '' }}</small>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="badge bg-warning text-dark">Level {{ $approver->approver_level }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('backend.payment-note.rule') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to Rules
                        </a>
                        <a href="{{ route('backend.payment-note.rule.edit', $step->id) }}" class="btn btn-success">
                            <i class="bi bi-pencil me-1"></i>Edit Rule
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

    .approver-item {
        background-color: #f8f9fa;
        transition: all 0.2s ease;
    }

    .approver-item:hover {
        background-color: #fff3cd;
        border-color: #fd7e14 !important;
    }

    .approver-number {
        font-size: 1rem;
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
            // Add any specific JavaScript for payment note rules if needed
        });
    </script>
@endpush
