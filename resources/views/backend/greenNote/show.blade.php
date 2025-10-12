@extends('backend.layouts.app')

@section('title', 'Expense Note Details')

@push('styles')
<link href="{{ asset('css/modern-design-system.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="modern-container">
    <!-- Modern Header -->
    <div class="modern-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="modern-page-title">
                    <i class="bi bi-receipt text-primary me-3"></i>Expense Note #{{ $note->formatted_order_no }}
                </h1>
                <p class="modern-page-subtitle">View and manage expense note details and approvals</p>
            </div>
            <div class="modern-action-group flex-wrap">
                <a href="{{ route('backend.note.download', ['id' => $note->id]) }}" class="btn-modern btn-modern-primary">
                    <i class="bi bi-download"></i>Download PDF
                </a>
                
                <!-- Multiple Invoices Button -->
                <a href="{{ route('backend.green-note.multiple-invoices.show', $note) }}" class="btn-modern btn-modern-secondary" title="Manage Multiple Invoices">
                    <i class="bi bi-receipt-cutoff"></i>Multiple Invoices
                </a>
                
                <!-- Hold/Unhold Button -->
                @if($note->status !== 'H')
                    <button type="button" class="btn-modern btn-modern-warning" data-bs-toggle="modal" data-bs-target="#holdModal" title="Put on Hold">
                        <i class="bi bi-pause-circle"></i>Hold
                    </button>
                @else
                    <button type="button" class="btn-modern btn-modern-success" data-bs-toggle="modal" data-bs-target="#unholdModal" title="Remove from Hold">
                        <i class="bi bi-play-circle"></i>Remove Hold
                    </button>
                @endif
                
                @if($note->status === 'A')
                    <a href="{{ route('backend.note.create.payment.note', $note->id) }}" class="btn-modern btn-modern-success">
                        <i class="bi bi-plus-circle"></i>Create Payment Note
                    </a>
                    
                    <!-- View Draft Payment Notes -->
                    <a href="{{ route('backend.payment-note.drafts') }}" class="btn-modern btn-modern-secondary" title="View Draft Payment Notes">
                        <i class="bi bi-file-earmark-text"></i>Draft Notes
                    </a>
                @endif
                
                <a href="{{ route('backend.note.index') }}" class="btn-modern btn-modern-secondary">
                    <i class="bi bi-arrow-left"></i>Back to Notes
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
        <a href="{{ route('backend.note.index') }}">Expense Notes</a>
        <span class="modern-breadcrumb-separator">/</span>
        <span>{{ $note->formatted_order_no }}</span>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Status Alert Section -->
            @if($note->status === 'H')
                <div class="alert alert-warning border-0 shadow-sm mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill text-warning me-3 fs-4"></i>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-1">Note is On Hold</h6>
                            <p class="mb-1"><strong>Reason:</strong> {{ $note->hold_reason ?? 'No reason specified' }}</p>
                            <small class="text-muted">
                                <strong>Hold Date:</strong> {{ $note->hold_date ? \Carbon\Carbon::parse($note->hold_date)->format('d/m/Y h:i A') : 'N/A' }} |
                                <strong>Hold By:</strong> {{ $note->holdBy->name ?? 'N/A' }}
                            </small>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Multiple Invoices Summary -->
            @if(!empty($note->invoices) && is_array($note->invoices) && count($note->invoices) > 1)
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-receipt-cutoff text-info me-3 fs-4"></i>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-1">Multiple Invoices Attached</h6>
                            <p class="mb-1">This expense note contains {{ count($note->invoices) }} invoices with a total value of ₹{{ number_format(collect($note->invoices)->sum('invoice_value'), 2) }}</p>
                            <a href="{{ route('backend.green-note.multiple-invoices.show', $note) }}" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-eye me-1"></i>View All Invoices
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Note Information Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>Note Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-12">
                            @include('backend.greenNote.partials.expense-approval-template', [
                                'note' => $note,
                                'documents' => $documents,
                                'departments' => $departments,
                            ])
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supporting Documents Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-paperclip text-primary me-2"></i>Supporting Documents
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Upload New Document -->
                    <div class="mb-4">
                        <form class="row g-3" action="{{ route('backend.documents.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="green_note_id" value="{{ $note->id }}">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="doc_name" name="name" required placeholder="Document Name">
                                    <label for="doc_name">Document Name</label>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="file" class="form-control @error('file_path') is-invalid @enderror"
                                           id="doc_file" name="file_path" required accept=".pdf,.doc,.docx,.xls,.xlsx,.csv">
                                    <label for="doc_file">Select File</label>
                                    @error('file_path')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload me-1"></i>Upload Document
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Documents Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">Document Name</th>
                                    <th width="20%">File</th>
                                    <th width="20%">Upload Date</th>
                                    <th width="15%">Uploaded By</th>
                                    <th width="15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documents as $index => $document)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $document->name }}</td>
                                        <td>
                                            @php
                                                $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
                                            @endphp

                                            @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                                <img src="{{ asset('notes/documents/' . $document->file_path) }}"
                                                     alt="Document Image" width="50" height="50" class="rounded">
                                            @else
                                                <a href="{{ asset('notes/documents/' . $document->file_path) }}"
                                                   target="_blank" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-file-earmark-text me-1"></i>View
                                                </a>
                                            @endif
                                        </td>
                                        <td>{{ $document->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') }}</td>
                                        <td>{{ $document->user->name }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ asset('notes/documents/' . $document->file_path) }}"
                                                   download="{{ $document->name }}" class="btn btn-outline-success btn-sm">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                @if (\Auth::id() == 1)
                                                    <form action="{{ route('backend.documents.destroy', $document->id) }}"
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                                                onclick="return confirm('Are you sure you want to delete this document?')">
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
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-chat-dots text-primary me-2"></i>Comments ({{ $note->comments->count() }})
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Existing Comments -->
                    @foreach ($note->comments as $comment)
                        @if (is_null($comment->parent_id))
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">{{ $comment->user->name }}</h6>
                                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                    <small class="text-muted">{{ $comment->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') }}</small>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">{{ $comment->comment }}</p>

                                    <!-- Replies -->
                                    @foreach ($comment->replies as $reply)
                                        <div class="border-start border-3 ps-3 ms-3 mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">{{ $reply->user->name }}</h6>
                                                <small class="text-muted">{{ $reply->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') }}</small>
                                            </div>
                                            <p class="mb-0">{{ $reply->comment }}</p>
                                        </div>
                                    @endforeach

                                    <!-- Action Buttons -->
                                    <div class="mt-3">
                                        @if ($comment->user_id == Auth::id())
                                            <a href="#" class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse"
                                               data-bs-target="#editForm-{{ $comment->id }}">
                                                <i class="bi bi-pencil me-1"></i>Edit
                                            </a>
                                        @endif
                                        <a href="#" class="btn btn-outline-info btn-sm" data-bs-toggle="collapse"
                                           data-bs-target="#replyForm-{{ $comment->id }}">
                                            <i class="bi bi-reply me-1"></i>Reply
                                        </a>
                                        <span class="badge bg-light text-dark ms-2">{{ $comment->replies->count() }} {{ Str::plural('Reply', $comment->replies->count()) }}</span>
                                    </div>

                                    <!-- Reply Form -->
                                    <div id="replyForm-{{ $comment->id }}" class="collapse mt-3">
                                        <form action="{{ route('backend.comments.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="note_id" value="{{ $note->id }}">
                                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                            <div class="mb-3">
                                                <textarea name="comment" class="form-control" required rows="3"
                                                          placeholder="Write a reply..."></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm">Post Reply</button>
                                        </form>
                                    </div>

                                    <!-- Edit Form -->
                                    <div id="editForm-{{ $comment->id }}" class="collapse mt-3">
                                        <form action="{{ route('backend.comments.update', $comment->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-3">
                                                <textarea name="comment" class="form-control" required rows="3">{{ $comment->comment }}</textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                            <a href="#" class="btn btn-secondary btn-sm" data-bs-toggle="collapse"
                                               data-bs-target="#editForm-{{ $comment->id }}">Cancel</a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <!-- Add Comment Form -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Add New Comment</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('backend.comments.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="note_id" value="{{ $note->id }}">
                                <div class="mb-3">
                                    <textarea name="comment" class="form-control" required rows="4"
                                              placeholder="Write your comment here..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-1"></i>Post Comment
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Approval Flow Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-diagram-3 text-primary me-2"></i>Approval Flow
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="position-relative">
                        <!-- Vertical Line -->
                        <div class="border-start border-3 border-primary position-absolute"
                             style="height: 100%; left: 10px; top: 0;"></div>

                        <!-- Steps -->
                        @foreach ($note->approvalLogs as $index => $step)
                            <div class="d-flex align-items-start position-relative mb-4" style="padding-left: 30px;">
                                <!-- Step Dot -->
                                <div class="position-absolute bg-primary rounded-circle"
                                     style="width: 12px; height: 12px; left: 5px; top: 6px;"></div>

                                <!-- Step Info -->
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <p class="fw-bold mb-0">Step {{ $index + 1 }}</p>
                                        @if ($step->status == 'R' && $step->comments)
                                            <small class="text-danger">Rejected</small>
                                        @endif
                                    </div>

                                    <p class="text-muted small mb-1">
                                        {{ $index == 0 ? 'Maker' : 'Reviewer' }}: {{ $step->reviewer->name }}
                                    </p>

                                    <p class="text-muted small mb-1">
                                        Date: {{ $step->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') }}
                                    </p>

                                    @if ($step->status == 'R' && $step->comments)
                                        <p class="text-muted small mb-1">
                                            Remarks: {{ $step->comments }}
                                        </p>
                                    @endif

                                    <div>
                                        @if ($step->status == 'A')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($step->status == 'P')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($step->status == 'R')
                                            <span class="badge bg-danger">Rejected</span>
                                        @elseif($step->status == 'PMPL')
                                            <span class="badge bg-info">Sent for PMC</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $step->status }}</span>
                                        @endif
                                    </div>

                                    @if (!$loop->last)
                                        <div class="position-absolute start-0 top-100 translate-middle-y border-start border-2"
                                             style="height: 30px; left: 6px;"></div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <!-- Next Approver (if applicable) -->
                        @if ($note->status != 'A')
                            @if ($note->approvalLogs->last()?->approvalStep && $note->approvalLogs->last()?->approvalStep->nextOnApprove && $note->approvalLogs->last()?->status == 'A')
                                <div class="d-flex align-items-center position-relative mb-4" style="padding-left: 30px;">
                                    <div class="position-absolute bg-primary rounded-circle"
                                         style="width: 12px; height: 12px; left: 5px;"></div>
                                    <div>
                                        <p class="fw-bold mb-1">Next Approver:</p>
                                        <p class="text-muted small">{{ $note->approvalLogs->last()->approvalStep->nextOnApprove->name }}</p>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Approval Actions Section -->
            @php
                $userRoles = auth()->user()->getRoleNames();
                $lastStep = $note->approvalLogs->last();
                $showButton = false;

                if ($lastStep && $lastStep->approvalStep && $lastStep->approvalStep->next_on_approve == auth()->user()->id && $lastStep->status == 'A' && $lastStep->reviewer_id !== auth()->user()->id) {
                    $showButton = true;
                }
            @endphp

            @if (!$userRoles->contains('Hr And Admin') && !$userRoles->contains('Qs'))
                @if ($showButton)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-check-circle text-primary me-2"></i>Approval Actions
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-grid gap-2">
                                <form id="approveForm" action="{{ route('backend.approval.approvalLogUpdate', $note->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="A">
                                    <button type="submit" class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-2" id="approveBtn">
                                        <span id="approveSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        <i class="bi bi-check-circle me-1"></i>Approve
                                    </button>
                                </form>

                                <button type="button" class="btn btn-danger" id="showRemarksBtn">
                                    <i class="bi bi-x-circle me-1"></i>Reject
                                </button>

                                <!-- Remarks Section (hidden initially) -->
                                <div id="remarksSection" style="display: none;">
                                    <form id="rejectForm" action="{{ route('backend.approval.approvalLogUpdate', $note->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="R">
                                        <div class="mb-3">
                                            <label for="remarks" class="form-label">Rejection Remarks</label>
                                            <textarea name="remarks" id="remarks" class="form-control" placeholder="Please provide reason for rejection..." required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2" id="rejectSubmitBtn">
                                            <span id="rejectSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                            <i class="bi bi-send me-1"></i>Submit Rejection
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
    
    <!-- Hold Modal -->
    <div class="modal fade" id="holdModal" tabindex="-1" aria-labelledby="holdModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="holdModalLabel">
                        <i class="bi bi-pause-circle text-warning me-2"></i>Put Note on Hold
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('backend.green-note.hold', $note) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="hold_reason" class="form-label">Reason for Hold <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="hold_reason" name="hold_reason" rows="3" required 
                                      placeholder="Please specify the reason for putting this note on hold..."></textarea>
                        </div>
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> Putting this note on hold will pause all approval processes until the hold is removed.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-pause-circle me-1"></i>Put on Hold
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Unhold Modal -->
    <div class="modal fade" id="unholdModal" tabindex="-1" aria-labelledby="unholdModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unholdModalLabel">
                        <i class="bi bi-play-circle text-success me-2"></i>Remove Hold
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('backend.green-note.remove-hold', $note) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Current Hold Details:</strong><br>
                            <strong>Reason:</strong> {{ $note->hold_reason ?? 'No reason specified' }}<br>
                            <strong>Hold Date:</strong> {{ $note->hold_date ? \Carbon\Carbon::parse($note->hold_date)->format('d/m/Y h:i A') : 'N/A' }}<br>
                            <strong>Hold By:</strong> {{ $note->holdBy->name ?? 'N/A' }}
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_status" class="form-label">Restore Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="new_status" name="new_status" required>
                                <option value="">Select status to restore...</option>
                                <option value="P" selected>Pending (P) - Resume approval process</option>
                                <option value="D">Draft (D) - Return to draft</option>
                                <option value="S">Submitted (S) - Mark as submitted</option>
                            </select>
                            <small class="text-muted">Select the status to restore the note to after removing hold.</small>
                        </div>
                        
                        <p>Are you sure you want to remove the hold from this note?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-play-circle me-1"></i>Remove Hold
                        </button>
                    </div>
                </form>
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
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }

    .badge {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    /* Approval flow styling */
    .border-start {
        border-color: #0d6efd !important;
    }

    .bg-primary {
        background-color: #0d6efd !important;
    }

    /* Form styling */
    .form-floating > .form-control,
    .form-floating > .form-select {
        height: calc(3.5rem + 2px);
    }

    /* Table styling */
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
        background-color: #f8f9fa;
        color: #495057;
    }

    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
        color: #212529;
    }

    /* Comment styling */
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    .border-start {
        border-color: #0d6efd !important;
    }

    /* Loading spinners */
    .spinner-border-sm {
        color: #0d6efd;
    }

    /* Hold modals should use default Bootstrap z-index (now that global backdrop is fixed) */
    #holdModal,
    #unholdModal {
        z-index: 1055;
    }
</style>
@endpush

@push('script')
    <script>
        // Show remarks when Reject clicked
        document.getElementById('showRemarksBtn')?.addEventListener('click', function() {
            document.getElementById('remarksSection').style.display = 'block';
            this.style.display = 'none';
        });

        // Approve button logic
        document.getElementById('approveForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('approveBtn');
            const spinner = document.getElementById('approveSpinner');
            btn.disabled = true;
            spinner.classList.remove('d-none');
        });

        // Reject submit logic
        document.getElementById('rejectForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('rejectSubmitBtn');
            const spinner = document.getElementById('rejectSpinner');
            btn.disabled = true;
            spinner.classList.remove('d-none');
        });

    </script>
@endpush
