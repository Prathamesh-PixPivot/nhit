@extends('backend.layouts.app')

@section('title', 'Draft Payment Notes')

@section('content')
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="bi bi-file-earmark-text me-2"></i>Draft Payment Notes
                    </h2>
                    <p class="text-muted mb-0">Manage and convert draft payment notes to active status</p>
                </div>
                <div class="d-flex gap-2">
                    @if(auth()->user()->hasRole('Super Admin'))
                        <a href="{{ route('backend.payment-note.create-superadmin') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Create Payment Note
                        </a>
                    @endif
                    <a href="{{ route('backend.payment-note.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>All Payment Notes
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
                    <li class="breadcrumb-item active" aria-current="page">Drafts</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="d-grid">
                                <button type="button" class="btn btn-outline-success" onclick="convertAllDrafts()">
                                    <i class="bi bi-check-circle me-1"></i>Convert All to Active
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <button type="button" class="btn btn-outline-info" onclick="filterByAutoCreated()">
                                    <i class="bi bi-funnel me-1"></i>Filter Auto-Created
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <button type="button" class="btn btn-outline-warning" onclick="filterByManual()">
                                    <i class="bi bi-funnel me-1"></i>Filter Manual
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                    <i class="bi bi-x-circle me-1"></i>Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <!-- Main Drafts Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-file-earmark-text text-primary me-2"></i>Draft Payment Notes
                        </h5>
                        <div class="d-flex gap-2">
                            <span class="badge bg-info fs-6">{{ $drafts->count() }} Drafts</span>
                            @if(auth()->user()->hasRole('Super Admin'))
                                <a href="{{ route('backend.payment-note.create-superadmin') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus"></i> Create Payment Note
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">

                    @if(session('success'))
                        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-3 fs-4"></i>
                                <div class="flex-grow-1">
                                    <h6 class="alert-heading mb-1">Success!</h6>
                                    <p class="mb-0">{{ session('success') }}</p>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill text-danger me-3 fs-4"></i>
                                <div class="flex-grow-1">
                                    <h6 class="alert-heading mb-1">Error!</h6>
                                    <p class="mb-0">{{ session('error') }}</p>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($drafts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover datatable" id="draftsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th width="10%">S.No.</th>
                                        <th width="15%">Payment Note No.</th>
                                        <th width="15%">Green Note No.</th>
                                        <th width="20%">Subject</th>
                                        <th width="12%">Created By</th>
                                        <th width="10%">Created Date</th>
                                        <th width="8%">Type</th>
                                        <th width="15%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($drafts as $index => $draft)
                                        <tr class="draft-row" data-auto-created="{{ $draft->auto_created ? 'true' : 'false' }}">
                                            <td>
                                                <input type="checkbox" class="form-check-input draft-checkbox" value="{{ $draft->id }}">
                                            </td>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        <i class="bi bi-file-earmark-text text-primary fs-5"></i>
                                                    </div>
                                                    <div>
                                                        <strong class="text-primary">{{ $draft->note_no }}</strong>
                                                        <br><span class="badge bg-warning text-dark">Draft</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($draft->greenNote)
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <i class="bi bi-link-45deg text-success"></i>
                                                        </div>
                                                        <div>
                                                            <a href="{{ route('backend.green-note.show', $draft->greenNote) }}" 
                                                               class="text-decoration-none fw-bold">
                                                                {{ $draft->greenNote->formatted_order_no }}
                                                            </a>
                                                            <br><small class="text-muted">{{ $draft->greenNote->department->name ?? 'N/A' }}</small>
                                                        </div>
                                                    </div>
                                                @elseif($draft->reimbursementNote)
                                                    <span class="badge bg-info">Reimbursement Note</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;" title="{{ $draft->subject }}">
                                                    <strong>{{ Str::limit($draft->subject, 50) }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                            <span class="text-white fw-bold small">{{ strtoupper(substr($draft->createdBy->name ?? $draft->user->name, 0, 1)) }}</span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $draft->createdBy->name ?? $draft->user->name }}</strong>
                                                        <br><small class="text-muted">{{ Str::limit($draft->createdBy->email ?? $draft->user->email, 20) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $draft->created_at->format('d/m/Y') }}</strong>
                                                    <br><small class="text-muted">{{ $draft->created_at->format('H:i A') }}</small>
                                                    <br><small class="text-info">{{ $draft->created_at->diffForHumans() }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($draft->auto_created)
                                                    <span class="badge bg-info d-flex align-items-center">
                                                        <i class="bi bi-robot me-1"></i>Auto
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary d-flex align-items-center">
                                                        <i class="bi bi-person me-1"></i>Manual
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('backend.payment-note.show', $draft) }}" 
                                                       class="btn btn-sm btn-outline-info" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('backend.payment-note.edit', $draft) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit Draft">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('backend.payment-note.convert-to-active', $draft) }}" 
                                                          method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Are you sure you want to convert this draft to active payment note? This action cannot be undone.')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Convert to Active">
                                                            <i class="bi bi-check-circle"></i>
                                                        </button>
                                                    </form>
                                                    @if(auth()->user()->can('delete-payment-note') || 
                                                        $draft->created_by === auth()->id() || 
                                                        $draft->user_id === auth()->id())
                                                        <form action="{{ route('backend.payment-note.delete-draft', $draft) }}" 
                                                              method="POST" class="d-inline" 
                                                              onsubmit="return confirm('Are you sure you want to delete this draft? This action cannot be undone.')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Draft">
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
                        
                        <!-- Bulk Actions -->
                        <div class="border-top pt-3 mt-3" id="bulkActions" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Selected: <strong id="selectedCount">0</strong> drafts</span>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success" onclick="bulkConvertToActive()">
                                        <i class="bi bi-check-circle me-1"></i>Convert Selected to Active
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="bulkDelete()">
                                        <i class="bi bi-trash me-1"></i>Delete Selected
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-file-earmark-text display-1 text-muted"></i>
                            </div>
                            <h4 class="text-muted mb-3">No Draft Payment Notes Found</h4>
                            <p class="text-muted mb-4">Draft payment notes will appear here when green notes are approved or when manually created.</p>
                            <div class="d-flex justify-content-center gap-2">
                                @if(auth()->user()->hasRole('Super Admin'))
                                    <a href="{{ route('backend.payment-note.create-superadmin') }}" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-1"></i>Create Payment Note
                                    </a>
                                @endif
                                <a href="{{ route('backend.note.index') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-file-earmark-check me-1"></i>View Green Notes
                                </a>
                            </div>
                        </div>
                    @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics Card --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Draft Statistics</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $drafts->count() }}</h3>
                                        <p class="mb-0">Total Drafts</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $drafts->where('auto_created', true)->count() }}</h3>
                                        <p class="mb-0">Auto Created</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-secondary text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $drafts->where('auto_created', false)->count() }}</h3>
                                        <p class="mb-0">Manual Created</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $drafts->where('created_at', '>=', now()->startOfDay())->count() }}</h3>
                                        <p class="mb-0">Today's Drafts</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
<script>
// Auto-hide success/error messages after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);

// Checkbox functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const draftCheckboxes = document.querySelectorAll('.draft-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    
    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            draftCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }
    
    // Individual checkbox functionality
    draftCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActions();
            updateSelectAllState();
        });
    });
    
    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.draft-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (selectedCount) {
            selectedCount.textContent = count;
        }
        
        if (bulkActions) {
            bulkActions.style.display = count > 0 ? 'block' : 'none';
        }
    }
    
    function updateSelectAllState() {
        const totalCheckboxes = draftCheckboxes.length;
        const checkedCheckboxes = document.querySelectorAll('.draft-checkbox:checked').length;
        
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = totalCheckboxes > 0 && checkedCheckboxes === totalCheckboxes;
            selectAllCheckbox.indeterminate = checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes;
        }
    }
});

// Filter functions
function filterByAutoCreated() {
    const rows = document.querySelectorAll('.draft-row');
    rows.forEach(row => {
        if (row.dataset.autoCreated === 'true') {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function filterByManual() {
    const rows = document.querySelectorAll('.draft-row');
    rows.forEach(row => {
        if (row.dataset.autoCreated === 'false') {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function clearFilters() {
    const rows = document.querySelectorAll('.draft-row');
    rows.forEach(row => {
        row.style.display = '';
    });
}

// Bulk actions
function bulkConvertToActive() {
    const checkedBoxes = document.querySelectorAll('.draft-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select at least one draft to convert.');
        return;
    }
    
    if (confirm(`Are you sure you want to convert ${checkedBoxes.length} draft(s) to active payment notes? This action cannot be undone.`)) {
        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        // Here you would implement the bulk conversion logic
        console.log('Converting drafts:', ids);
        // For now, just show a message
        alert('Bulk conversion feature will be implemented in the backend.');
    }
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.draft-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select at least one draft to delete.');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${checkedBoxes.length} draft(s)? This action cannot be undone.`)) {
        const ids = Array.from(checkedBoxes).map(cb => cb.value);
        // Here you would implement the bulk deletion logic
        console.log('Deleting drafts:', ids);
        // For now, just show a message
        alert('Bulk deletion feature will be implemented in the backend.');
    }
}

function convertAllDrafts() {
    const totalDrafts = document.querySelectorAll('.draft-row').length;
    if (totalDrafts === 0) {
        alert('No drafts available to convert.');
        return;
    }
    
    if (confirm(`Are you sure you want to convert all ${totalDrafts} draft(s) to active payment notes? This action cannot be undone.`)) {
        // Here you would implement the convert all logic
        console.log('Converting all drafts');
        // For now, just show a message
        alert('Convert all feature will be implemented in the backend.');
    }
}
</script>
@endpush
