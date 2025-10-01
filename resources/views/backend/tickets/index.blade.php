@extends('backend.layouts.app')

@section('title', 'Support Tickets Management')

@section('content')
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-success mb-1">
                        <i class="bi bi-ticket-detailed me-2"></i>Support Tickets Management
                    </h2>
                    <p class="text-muted mb-0">Track and manage customer support tickets</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('backend.tickets.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Create New Ticket
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
                    <li class="breadcrumb-item active" aria-current="page">Tickets</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="search" placeholder="Search tickets..."
                                       value="{{ request('search') }}">
                                <label for="search">Search</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="O" {{ request('status') == 'O' ? 'selected' : '' }}>Open</option>
                                    <option value="IP" {{ request('status') == 'IP' ? 'selected' : '' }}>In Progress</option>
                                    <option value="R" {{ request('status') == 'R' ? 'selected' : '' }}>Resolved</option>
                                    <option value="C" {{ request('status') == 'C' ? 'selected' : '' }}>Closed</option>
                                </select>
                                <label for="status">Status</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select name="priority" class="form-select">
                                    <option value="">All Priority</option>
                                    <option value="L" {{ request('priority') == 'L' ? 'selected' : '' }}>Low</option>
                                    <option value="M" {{ request('priority') == 'M' ? 'selected' : '' }}>Medium</option>
                                    <option value="H" {{ request('priority') == 'H' ? 'selected' : '' }}>High</option>
                                </select>
                                <label for="priority">Priority</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-1"></i>Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-table text-primary me-2"></i>All Support Tickets
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Tickets Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="15%">Name</th>
                                    <th width="25%">Error/Issue</th>
                                    <th width="10%">Attachments</th>
                                    <th width="10%">Priority</th>
                                    <th width="10%">Status</th>
                                    <th width="15%">Created Date</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($tickets->count() > 0)
                                    @foreach ($tickets as $i => $ticket)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <h6 class="mb-0">{{ $ticket->name }}</h6>
                                                        <small class="text-muted">{{ $ticket->entity_name }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{{ $ticket->error }}</strong>
                                                @if($ticket->description)
                                                    <br><small class="text-muted">{{ Str::limit(strip_tags($ticket->description), 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $attachments = json_decode($ticket->attachments, true) ?? [];
                                                    $first = $attachments[0] ?? null;
                                                    $remainingCount = count($attachments) - 1;
                                                @endphp

                                                @if ($first)
                                                    @if (Str::endsWith($first, ['.jpg', '.jpeg', '.png', '.gif']))
                                                        <img src="{{ asset('storage/bugs/' . $first) }}" width="40" height="40"
                                                             class="img-thumbnail rounded" data-bs-toggle="modal"
                                                             data-bs-target="#attachmentModal{{ $ticket->id }}" style="cursor:pointer;">
                                                    @elseif(Str::endsWith($first, ['.mp4', '.mov']))
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                             style="width: 40px; height: 40px;" data-bs-toggle="modal"
                                                             data-bs-target="#attachmentModal{{ $ticket->id }}">
                                                            <i class="bi bi-camera-video text-muted"></i>
                                                        </div>
                                                    @else
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                             style="width: 40px; height: 40px;">
                                                            <i class="bi bi-file-earmark text-muted"></i>
                                                        </div>
                                                    @endif

                                                    @if ($remainingCount > 0)
                                                        <span class="badge bg-secondary ms-1">+{{ $remainingCount }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No files</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $priorityColors = [
                                                        'L' => 'success',
                                                        'M' => 'warning',
                                                        'H' => 'danger',
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $priorityColors[$ticket->priority] ?? 'secondary' }}">
                                                    {{ $ticket->priority_label }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'O' => 'secondary',
                                                        'IP' => 'info',
                                                        'R' => 'primary',
                                                        'C' => 'success',
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $statusColors[$ticket->status] ?? 'dark' }}">
                                                    {{ $ticket->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <small>{{ $ticket->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y') }}</small>
                                                <br>
                                                <small class="text-muted">{{ $ticket->created_at->setTimezone('Asia/Kolkata')->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if (count($attachments) > 0)
                                                        <a href="{{ route('backend.tickets.downloadAll', $ticket->id) }}"
                                                           class="btn btn-outline-primary btn-sm" title="Download All">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('backend.tickets.show', $ticket->id) }}"
                                                       class="btn btn-outline-info btn-sm" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('backend.tickets.edit', $ticket->id) }}"
                                                       class="btn btn-outline-warning btn-sm" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('backend.tickets.destroy', $ticket->id) }}"
                                                          method="POST" style="display:inline;" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                                                title="Delete" onclick="return confirm('Are you sure you want to delete this ticket?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Attachment Modal -->
                                        <div class="modal fade" id="attachmentModal{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Ticket Attachments - {{ $ticket->error }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div id="carousel{{ $ticket->id }}" class="carousel slide" data-bs-ride="carousel">
                                                            <div class="carousel-inner">
                                                                @foreach ($attachments as $index => $file)
                                                                    @php $path = asset('storage/bugs/' . $file); @endphp
                                                                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                                        @if (Str::endsWith($file, ['.jpg', '.jpeg', '.png', '.gif']))
                                                                            <img src="{{ asset('storage/bugs/' . $file) }}" class="d-block w-100" style="max-height:500px; object-fit:contain;">
                                                                        @elseif(Str::endsWith($file, ['.mp4', '.mov']))
                                                                            <video controls class="d-block w-100" style="max-height:500px;">
                                                                                <source src="{{ asset('storage/bugs/' . $file) }}">
                                                                                Your browser does not support the video tag.
                                                                            </video>
                                                                        @else
                                                                            <p class="text-center">Unsupported file: <a href="{{ $path }}" target="_blank">{{ $file }}</a></p>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            @if (count($attachments) > 1)
                                                                <button class="carousel-control-prev" type="button" data-bs-target="#carousel{{ $ticket->id }}" data-bs-slide="prev">
                                                                    <span class="carousel-control-prev-icon"></span>
                                                                </button>
                                                                <button class="carousel-control-next" type="button" data-bs-target="#carousel{{ $ticket->id }}" data-bs-slide="next">
                                                                    <span class="carousel-control-next-icon"></span>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="100%" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            No tickets found matching your criteria.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($tickets->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $tickets->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
        background-color: #f8f9fa;
        color: #495057;
    }

    /* Table data styling */
    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
        color: #212529;
    }

    /* Table responsive styling */
    .table-responsive {
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    /* Card styling */
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* Card hover effects */
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }

    /* Status badges with proper colors */
    .badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }

    .bg-secondary { background-color: #6c757d !important; }
    .bg-info { background-color: #0dcaf0 !important; }
    .bg-primary { background-color: #0d6efd !important; }
    .bg-success { background-color: #198754 !important; }
    .bg-warning { background-color: #ffc107 !important; }
    .bg-danger { background-color: #dc3545 !important; }

    /* Action buttons with proper styling */
    .btn-group .btn {
        margin-right: 0.125rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-group .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    /* Filter form styling */
    .form-floating > .form-control,
    .form-floating > .form-select {
        height: calc(3.5rem + 2px);
    }

    /* Table hover effects */
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }

    /* Modal improvements */
    .modal-content {
        border-radius: 0.5rem;
        border: none;
    }

    .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        border-radius: 0.5rem 0.5rem 0 0;
    }

    /* Loading states */
    .spinner-border-sm {
        color: #0d6efd;
    }

    /* Empty state styling */
    .dataTables_empty {
        text-align: center;
        padding: 3rem;
        color: #6c757d;
        font-style: italic;
    }

    /* Custom badge styling for priority and status */
    .badge-priority-low {
        background-color: #198754 !important;
        color: white !important;
    }

    .badge-priority-medium {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }

    .badge-priority-high {
        background-color: #dc3545 !important;
        color: white !important;
    }

    .badge-status-open {
        background-color: #6c757d !important;
        color: white !important;
    }

    .badge-status-progress {
        background-color: #0dcaf0 !important;
        color: white !important;
    }

    .badge-status-resolved {
        background-color: #0d6efd !important;
        color: white !important;
    }

    .badge-status-closed {
        background-color: #198754 !important;
        color: white !important;
    }
</style>
