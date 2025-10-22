@extends('backend.layouts.app')

@section('title', 'Bank Letter Details')

@section('content')
<div class="modern-container">
    <!-- Modern Header -->
    <div class="modern-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="modern-page-title">
                    <i class="bi bi-file-earmark-text text-primary me-3"></i>Bank Letter Details
                </h1>
                <p class="modern-page-subtitle">Bank Letter No: <strong class="text-primary">{{ $slNo }}</strong></p>
            </div>
            <div class="d-flex gap-3">
                <a href="{{ route('backend.bank-letter.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to List
                </a>
                @if($status === 'A')
                    <a href="{{ route('backend.bank-letter.download', $slNo) }}" class="btn btn-success">
                        <i class="bi bi-download me-1"></i>Download
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Modern Breadcrumb -->
    <div class="modern-breadcrumb">
        <a href="{{ route('backend.dashboard.index') }}">
            <i class="bi bi-house-door me-1"></i>Dashboard
        </a>
        <span class="modern-breadcrumb-separator">/</span>
        <a href="{{ route('backend.bank-letter.index') }}">Bank Letters</a>
        <span class="modern-breadcrumb-separator">/</span>
        <span>{{ $slNo }}</span>
    </div>

    <div class="row">
        <!-- Bank Letter Summary -->
        <div class="col-md-4">
            <div class="modern-card">
                <div class="modern-card-header">
                    <h3 class="mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>Summary
                    </h3>
                </div>
                <div class="modern-card-body">
                    <div class="summary-item">
                        <label>Bank Letter No:</label>
                        <strong class="text-primary">{{ $slNo }}</strong>
                    </div>
                    <div class="summary-item">
                        <label>Total Amount:</label>
                        <strong class="text-success">₹{{ number_format($totalAmount, 2) }}</strong>
                    </div>
                    <div class="summary-item">
                        <label>Number of Payments:</label>
                        <strong>{{ $payments->count() }}</strong>
                    </div>
                    <div class="summary-item">
                        <label>Status:</label>
                        @php
                            $statusLabels = [
                                'S' => '<span class="badge bg-warning">Pending Approval</span>',
                                'A' => '<span class="badge bg-success">Approved</span>',
                                'R' => '<span class="badge bg-danger">Rejected</span>',
                                'P' => '<span class="badge bg-info">Processing</span>',
                            ];
                        @endphp
                        {!! $statusLabels[$status] ?? '<span class="badge bg-secondary">Unknown</span>' !!}
                    </div>
                    <div class="summary-item">
                        <label>Created Date:</label>
                        <strong>{{ $payments->first()->created_at->format('d/m/Y h:i A') }}</strong>
                    </div>
                    <div class="summary-item">
                        <label>Created By:</label>
                        <strong>{{ $payments->first()->user->name ?? 'System' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Approval Timeline -->
            <div class="modern-card mt-4">
                <div class="modern-card-header">
                    <h3 class="mb-0">
                        <i class="bi bi-clock-history text-primary me-2"></i>Approval Timeline
                    </h3>
                </div>
                <div class="modern-card-body">
                    <div class="timeline">
                        @forelse($approvalLogs as $log)
                            <div class="timeline-item {{ $log->status === 'A' ? 'timeline-approved' : ($log->status === 'R' ? 'timeline-rejected' : 'timeline-pending') }}">
                                <div class="timeline-marker">
                                    @if($log->status === 'A')
                                        <i class="bi bi-check-circle"></i>
                                    @elseif($log->status === 'R')
                                        <i class="bi bi-x-circle"></i>
                                    @else
                                        <i class="bi bi-clock"></i>
                                    @endif
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <strong>{{ $log->reviewer->name ?? 'System' }}</strong>
                                        <small class="text-muted">{{ $log->created_at->format('d/m/Y h:i A') }}</small>
                                    </div>
                                    <div class="timeline-body">
                                        @if($log->status === 'A')
                                            <span class="text-success">Approved</span>
                                        @elseif($log->status === 'R')
                                            <span class="text-danger">Rejected</span>
                                        @else
                                            <span class="text-warning">Pending</span>
                                        @endif
                                        @if($log->comments)
                                            <p class="mb-0 mt-1"><em>"{{ $log->comments }}"</em></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted">
                                <i class="bi bi-clock-history fs-1"></i>
                                <p>No approval logs yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="col-md-8">
            <div class="modern-card">
                <div class="modern-card-header">
                    <h3 class="mb-0">
                        <i class="bi bi-list-ul text-primary me-2"></i>Payment Details
                    </h3>
                </div>
                <div class="modern-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Beneficiary</th>
                                    <th>Account Details</th>
                                    <th>Amount</th>
                                    <th>Purpose</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $index => $payment)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="beneficiary-info">
                                            <strong>{{ $payment->name_of_beneficiary ?? 'N/A' }}</strong>
                                            @if($payment->project)
                                                <small class="text-muted d-block">Project: {{ $payment->project }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="account-info">
                                            @if($payment->account_number)
                                                <strong>A/C: {{ $payment->account_number }}</strong><br>
                                            @endif
                                            @if($payment->name_of_bank)
                                                <small>{{ $payment->name_of_bank }}</small><br>
                                            @endif
                                            @if($payment->ifsc_code)
                                                <small class="text-muted">IFSC: {{ $payment->ifsc_code }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-success">₹{{ number_format($payment->amount, 2) }}</strong>
                                    </td>
                                    <td>
                                        <small>{{ $payment->purpose ?? 'Payment processing' }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-info">
                                    <th colspan="3" class="text-end">Total Amount:</th>
                                    <th><strong class="text-success">₹{{ number_format($totalAmount, 2) }}</strong></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Related Payment Notes -->
            <div class="modern-card mt-4">
                <div class="modern-card-header">
                    <h3 class="mb-0">
                        <i class="bi bi-link-45deg text-primary me-2"></i>Related Payment Notes
                    </h3>
                </div>
                <div class="modern-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Payment Note ID</th>
                                    <th>Type</th>
                                    <th>Vendor/Employee</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                    @if($payment->paymentNote)
                                    <tr>
                                        <td><strong>{{ $payment->paymentNote->id }}</strong></td>
                                        <td>
                                            @if($payment->paymentNote->greenNote)
                                                <span class="badge bg-primary">Expense Note</span>
                                            @elseif($payment->paymentNote->reimbursementNote)
                                                <span class="badge bg-info">Reimbursement</span>
                                            @else
                                                <span class="badge bg-secondary">Other</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($payment->paymentNote->greenNote)
                                                {{ $payment->paymentNote->greenNote->supplier->vendor_name ?? 'N/A' }}
                                            @elseif($payment->paymentNote->reimbursementNote)
                                                {{ $payment->paymentNote->reimbursementNote->selectUser 
                                                    ? $payment->paymentNote->reimbursementNote->selectUser->name 
                                                    : $payment->paymentNote->reimbursementNote->user->name }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td><strong>₹{{ number_format($payment->paymentNote->net_payable_round_off, 2) }}</strong></td>
                                        <td>
                                            @php
                                                $noteStatusLabels = [
                                                    'D' => '<span class="badge bg-dark">Draft</span>',
                                                    'S' => '<span class="badge bg-warning">Pending</span>',
                                                    'A' => '<span class="badge bg-success">Approved</span>',
                                                    'R' => '<span class="badge bg-danger">Rejected</span>',
                                                    'PA' => '<span class="badge bg-info">Payment Approved</span>',
                                                ];
                                            @endphp
                                            {!! $noteStatusLabels[$payment->paymentNote->status] ?? '<span class="badge bg-secondary">Unknown</span>' !!}
                                        </td>
                                        <td>
                                            <a href="{{ route('backend.payment-note.show', $payment->paymentNote->id) }}" 
                                               class="btn btn-outline-info btn-sm" title="View Payment Note">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Modern Container Styles */
    .modern-container {
        padding: 1.5rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 100vh;
    }

    .modern-header {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .modern-page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .modern-page-subtitle {
        color: #6c757d;
        font-size: 1.1rem;
        margin-bottom: 0;
    }

    .modern-breadcrumb {
        background: white;
        border-radius: 0.75rem;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        font-size: 0.95rem;
    }

    .modern-breadcrumb a {
        color: #007bff;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .modern-breadcrumb a:hover {
        color: #0056b3;
    }

    .modern-breadcrumb-separator {
        margin: 0 0.75rem;
        color: #6c757d;
    }

    .modern-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .modern-card-header {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        padding: 1.5rem 2rem;
        border-bottom: 1px solid rgba(0,0,0,0.08);
    }

    .modern-card-body {
        padding: 1.5rem 2rem;
    }

    /* Summary Styles */
    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f8f9fa;
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .summary-item label {
        color: #6c757d;
        font-weight: 500;
        margin-bottom: 0;
    }

    /* Timeline Styles */
    .timeline {
        position: relative;
        padding-left: 2rem;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 1rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .timeline-marker {
        position: absolute;
        left: -2rem;
        top: 0.25rem;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        z-index: 2;
    }

    .timeline-approved .timeline-marker {
        background: #28a745;
        color: white;
    }

    .timeline-rejected .timeline-marker {
        background: #dc3545;
        color: white;
    }

    .timeline-pending .timeline-marker {
        background: #ffc107;
        color: white;
    }

    .timeline-content {
        background: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-left: 1rem;
    }

    .timeline-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .timeline-body {
        font-size: 0.9rem;
    }

    /* Table Styles */
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        color: #495057;
    }

    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
        color: #212529;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0,123,255,0.05);
    }

    .beneficiary-info strong {
        color: #2c3e50;
    }

    .account-info {
        font-size: 0.85rem;
    }

    .account-info strong {
        color: #495057;
    }
</style>
@endpush
