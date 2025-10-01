@extends('backend.layouts.app')

@section('title', 'Reimbursement Note Details - ' . ($note->note_no ?? 'N/A'))

@section('content')
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-success mb-1">
                        <i class="bi bi-receipt-cutoff me-2"></i>Reimbursement Note Details
                    </h2>
                    <p class="text-muted mb-0">View and manage reimbursement note information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('backend.reimbursement-note.download', ['id' => $note->id]) }}" class="btn btn-success">
                        <i class="bi bi-download me-1"></i>Download PDF
                    </a>
                    <a href="{{ route('backend.reimbursement-note.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Notes
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
                        <a href="{{ route('backend.reimbursement-note.index') }}">Reimbursement Notes</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $note->note_no ?? 'N/A' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Note Information Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle text-success me-2"></i>Note Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-12">
                            @include('backend.reimbursementNote.partials.reimbursement-template', [
                                'note' => $note,
                            ])
                        </div>
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
                                <input type="hidden" name="reimbursement_note_id" value="{{ $note->id }}">
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
            <!-- Attached Files Section -->
            @if ($note->file_path)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-paperclip text-primary me-2"></i>Attached Files
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10%">#</th>
                                        <th width="30%">Preview</th>
                                        <th width="20%">File Type</th>
                                        <th width="20%">Upload Date</th>
                                        <th width="20%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (json_decode($note->file_path, true) as $key => $file)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                @php
                                                    $extension = pathinfo($file, PATHINFO_EXTENSION);
                                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                @endphp

                                                @if ($isImage)
                                                    <img src="{{ asset('storage/rn/' . $file) }}" alt="Preview" width="60" height="60" class="rounded">
                                                @elseif (strtolower($extension) == 'pdf')
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                        <i class="bi bi-file-earmark-pdf text-danger fs-4"></i>
                                                    </div>
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                        <i class="bi bi-file-earmark text-muted fs-4"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($isImage)
                                                    <span class="badge bg-success">Image</span>
                                                @elseif (strtolower($extension) == 'pdf')
                                                    <span class="badge bg-danger">PDF</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ strtoupper($extension) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $note->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') }}</td>
                                            <td>
                                                <a href="{{ asset('storage/rn/' . $file) }}" download class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-download me-1"></i>Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Approval Actions Section -->
            @can('approver-reimbursement-note')
                @if ($note->status !== 'D')
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-check-circle text-primary me-2"></i>Approval Actions
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-grid gap-2">
                                @if ($note->approver_id == auth()->user()->id)
                                    <form action="{{ route('backend.reimbursement-note.approvalLogUpdate', $note->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="A">
                                        <button type="submit" class="btn btn-success w-100 d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-check-circle me-1"></i>Approve
                                        </button>
                                    </form>
                                @endif

                                <button type="button" class="btn btn-danger" id="showRemarksBtn">
                                    <i class="bi bi-x-circle me-1"></i>Reject
                                </button>

                                <!-- Remarks Section (hidden initially) -->
                                <div id="remarksSection" style="display: none;">
                                    <form action="{{ route('backend.reimbursement-note.approvalLogUpdate', $note->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="R">
                                        <div class="mb-3">
                                            <label for="remarks" class="form-label">Rejection Remarks</label>
                                            <textarea name="remarks" id="remarks" class="form-control" placeholder="Please provide reason for rejection..." required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-send me-1"></i>Submit Rejection
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endcan
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

    .badge {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    /* Table styling */
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
        background-color: #fff3cd;
        color: #856404;
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

    /* Form styling */
    .form-control {
        border-radius: 0.375rem;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
    }

    /* Loading states */
    .spinner-border-sm {
        color: #198754;
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
    </script>
@endpush
