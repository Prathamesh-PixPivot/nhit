@extends('backend.layouts.app')

@section('title', 'Approve Bank Letter')

@section('content')
<div class="modern-container">
    <!-- Modern Header -->
    <div class="modern-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="modern-page-title">
                    <i class="bi bi-check-circle text-success me-3"></i>Approve Bank Letter
                </h1>
                <p class="modern-page-subtitle">Bank Letter No: <strong class="text-primary">{{ $slNo }}</strong></p>
            </div>
            <div class="d-flex gap-3">
                <a href="{{ route('backend.bank-letter.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to List
                </a>
                <a href="{{ route('backend.bank-letter.show-letter', $slNo) }}" class="btn btn-outline-info">
                    <i class="bi bi-eye me-1"></i>View Details
                </a>
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
        <span>Approve {{ $slNo }}</span>
    </div>

    <div class="row">
        <!-- Approval Form -->
        <div class="col-md-8">
            <div class="modern-card">
                <div class="modern-card-header">
                    <h3 class="mb-0">
                        <i class="bi bi-clipboard-check text-primary me-2"></i>Review & Approval
                    </h3>
                </div>
                <div class="modern-card-body">
                    <form action="{{ route('backend.bank-letter.process-approval', $slNo) }}" method="POST" id="approvalForm">
                        @csrf
                        
                        <!-- Bank Letter Summary -->
                        <div class="approval-summary mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="summary-card">
                                        <h5><i class="bi bi-file-earmark-text text-primary me-2"></i>Bank Letter Details</h5>
                                        <div class="summary-item">
                                            <span class="label">Letter No:</span>
                                            <strong class="text-primary">{{ $slNo }}</strong>
                                        </div>
                                        <div class="summary-item">
                                            <span class="label">Total Amount:</span>
                                            <strong class="text-success">₹{{ number_format($totalAmount, 2) }}</strong>
                                        </div>
                                        <div class="summary-item">
                                            <span class="label">Number of Payments:</span>
                                            <strong>{{ $payments->count() }}</strong>
                                        </div>
                                        <div class="summary-item">
                                            <span class="label">Created Date:</span>
                                            <strong>{{ $payments->first()->created_at->format('d/m/Y h:i A') }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="summary-card">
                                        <h5><i class="bi bi-person text-info me-2"></i>Creator Information</h5>
                                        <div class="summary-item">
                                            <span class="label">Created By:</span>
                                            <strong>{{ $payments->first()->user->name ?? 'System' }}</strong>
                                        </div>
                                        <div class="summary-item">
                                            <span class="label">Email:</span>
                                            <strong>{{ $payments->first()->user->email ?? 'N/A' }}</strong>
                                        </div>
                                        <div class="summary-item">
                                            <span class="label">Department:</span>
                                            <strong>{{ $payments->first()->user->department ?? 'N/A' }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Approval Decision -->
                        <div class="approval-decision mb-4">
                            <h5 class="mb-3"><i class="bi bi-hand-thumbs-up text-success me-2"></i>Your Decision</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check approval-option approval-approve">
                                        <input class="form-check-input" type="radio" name="status" id="approve" value="A" required>
                                        <label class="form-check-label" for="approve">
                                            <i class="bi bi-check-circle text-success me-2"></i>
                                            <strong>Approve</strong>
                                            <small class="d-block text-muted">Approve this bank letter for processing</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check approval-option approval-reject">
                                        <input class="form-check-input" type="radio" name="status" id="reject" value="R" required>
                                        <label class="form-check-label" for="reject">
                                            <i class="bi bi-x-circle text-danger me-2"></i>
                                            <strong>Reject</strong>
                                            <small class="d-block text-muted">Reject this bank letter with comments</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Comments Section -->
                        <div class="comments-section mb-4">
                            <h5 class="mb-3"><i class="bi bi-chat-text text-info me-2"></i>Comments</h5>
                            <div class="form-group">
                                <label for="comments" class="form-label">
                                    Add your comments <span class="text-muted">(Optional for approval, Required for rejection)</span>
                                </label>
                                <textarea class="form-control" id="comments" name="comments" rows="4" 
                                          placeholder="Enter your comments here...">{{ old('comments') }}</textarea>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Your comments will be visible to the creator and other approvers.
                                </div>
                                @error('comments')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="approval-actions">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirmDecision" required>
                                    <label class="form-check-label" for="confirmDecision">
                                        I confirm that I have reviewed all details and my decision is final
                                    </label>
                                </div>
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-outline-secondary me-2" onclick="history.back()">
                                        <i class="bi bi-arrow-left me-1"></i>Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                        <i class="bi bi-check-lg me-1"></i>Submit Decision
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Information -->
        <div class="col-md-4">
            <!-- Payment Details Summary -->
            <div class="modern-card mb-4">
                <div class="modern-card-header">
                    <h3 class="mb-0">
                        <i class="bi bi-list-ul text-primary me-2"></i>Payment Summary
                    </h3>
                </div>
                <div class="modern-card-body">
                    <div class="payment-summary">
                        @foreach($payments->take(3) as $payment)
                        <div class="payment-item">
                            <div class="payment-beneficiary">
                                <strong>{{ $payment->name_of_beneficiary ?? 'N/A' }}</strong>
                                @if($payment->project)
                                    <small class="text-muted d-block">{{ $payment->project }}</small>
                                @endif
                            </div>
                            <div class="payment-amount">
                                <strong class="text-success">₹{{ number_format($payment->amount, 2) }}</strong>
                            </div>
                        </div>
                        @endforeach
                        
                        @if($payments->count() > 3)
                        <div class="payment-item">
                            <div class="payment-beneficiary">
                                <em class="text-muted">... and {{ $payments->count() - 3 }} more payments</em>
                            </div>
                        </div>
                        @endif
                        
                        <hr>
                        <div class="payment-total">
                            <div class="payment-beneficiary">
                                <strong>Total Amount</strong>
                            </div>
                            <div class="payment-amount">
                                <strong class="text-success fs-5">₹{{ number_format($totalAmount, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Previous Approvals -->
            @if($approvalLogs->isNotEmpty())
            <div class="modern-card">
                <div class="modern-card-header">
                    <h3 class="mb-0">
                        <i class="bi bi-clock-history text-primary me-2"></i>Previous Approvals
                    </h3>
                </div>
                <div class="modern-card-body">
                    <div class="approval-history">
                        @foreach($approvalLogs as $log)
                        <div class="approval-item {{ $log->status === 'A' ? 'approved' : ($log->status === 'R' ? 'rejected' : 'pending') }}">
                            <div class="approval-header">
                                <div class="approval-user">
                                    <strong>{{ $log->reviewer->name ?? 'System' }}</strong>
                                    @if($log->status === 'A')
                                        <span class="badge bg-success ms-2">Approved</span>
                                    @elseif($log->status === 'R')
                                        <span class="badge bg-danger ms-2">Rejected</span>
                                    @else
                                        <span class="badge bg-warning ms-2">Pending</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $log->created_at->format('d/m/Y h:i A') }}</small>
                            </div>
                            @if($log->comments)
                            <div class="approval-comments">
                                <em>"{{ $log->comments }}"</em>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
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

    /* Approval Summary */
    .summary-card {
        background: #f8f9fa;
        border-radius: 0.75rem;
        padding: 1.5rem;
        height: 100%;
    }

    .summary-card h5 {
        margin-bottom: 1rem;
        color: #2c3e50;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .summary-item .label {
        color: #6c757d;
        font-weight: 500;
    }

    /* Approval Options */
    .approval-option {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .approval-option:hover {
        border-color: #007bff;
        background: #f0f8ff;
    }

    .approval-option input[type="radio"]:checked + label {
        color: #007bff;
    }

    .approval-approve input[type="radio"]:checked ~ * {
        border-color: #28a745;
        background: #f0fff4;
    }

    .approval-reject input[type="radio"]:checked ~ * {
        border-color: #dc3545;
        background: #fff5f5;
    }

    .approval-option .form-check-label {
        cursor: pointer;
        width: 100%;
    }

    /* Comments Section */
    .comments-section .form-control {
        border-radius: 0.5rem;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .comments-section .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }

    /* Action Buttons */
    .approval-actions {
        background: #f8f9fa;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .action-buttons .btn {
        min-width: 120px;
    }

    /* Payment Summary */
    .payment-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f8f9fa;
    }

    .payment-item:last-child {
        border-bottom: none;
    }

    .payment-total {
        background: #f0f8ff;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 0.5rem;
    }

    /* Approval History */
    .approval-item {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        border-left: 4px solid #e9ecef;
    }

    .approval-item.approved {
        background: #f0fff4;
        border-left-color: #28a745;
    }

    .approval-item.rejected {
        background: #fff5f5;
        border-left-color: #dc3545;
    }

    .approval-item.pending {
        background: #fffbf0;
        border-left-color: #ffc107;
    }

    .approval-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .approval-comments {
        font-size: 0.9rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }
</style>
@endpush

@push('script')
<script>
    $(document).ready(function() {
        // Enable/disable submit button based on confirmation checkbox
        $('#confirmDecision').on('change', function() {
            $('#submitBtn').prop('disabled', !this.checked);
        });

        // Handle approval decision changes
        $('input[name="status"]').on('change', function() {
            const status = $(this).val();
            const commentsField = $('#comments');
            
            if (status === 'R') {
                // Rejection requires comments
                commentsField.prop('required', true);
                commentsField.attr('placeholder', 'Please provide reason for rejection (Required)');
                $('.comments-section .form-text').html('<i class="bi bi-exclamation-triangle text-warning me-1"></i>Comments are required for rejection.');
            } else {
                // Approval doesn't require comments
                commentsField.prop('required', false);
                commentsField.attr('placeholder', 'Enter your comments here... (Optional)');
                $('.comments-section .form-text').html('<i class="bi bi-info-circle me-1"></i>Your comments will be visible to the creator and other approvers.');
            }
        });

        // Form submission with confirmation
        $('#approvalForm').on('submit', function(e) {
            const status = $('input[name="status"]:checked').val();
            const statusText = status === 'A' ? 'approve' : 'reject';
            
            if (!confirm(`Are you sure you want to ${statusText} this bank letter? This action cannot be undone.`)) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            $('#submitBtn').prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Processing...');
        });

        // Auto-resize textarea
        $('#comments').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
</script>
@endpush
