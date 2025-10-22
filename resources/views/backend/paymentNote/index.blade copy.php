@extends('backend.layouts.app')

@section('title', 'Payment Notes Management')

@section('content')
<div class="modern-container">
    <!-- Modern Header -->
    <div class="modern-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="modern-page-title">
                    <i class="bi bi-credit-card text-primary me-3"></i>Payment Notes
                </h1>
                <p class="modern-page-subtitle">Track and manage payment note requests and approvals</p>
            </div>
            <div class="d-flex gap-3">
                @can('create-payment-note')
                    <a href="{{ route('backend.payment-note.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Create Payment Note
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Modern Breadcrumb -->
    <div class="modern-breadcrumb">
        <a href="{{ route('backend.dashboard.index') }}">
            <i class="bi bi-house-door me-1"></i>Dashboard
        </a>
        <span class="modern-breadcrumb-separator">/</span>
        <span>Payment Notes</span>
    </div>

    <!-- Modern Status Tabs -->
    <div class="modern-tabs">
        <a href="{{ route('backend.payment-note.index', ['status' => 'all']) }}" 
           class="modern-tab {{ request('status') === 'all' ? 'active' : '' }}">
            <i class="bi bi-list-ul me-2"></i>All Notes
        </a>
        <a href="{{ route('backend.payment-note.index', ['status' => 'S']) }}" 
           class="modern-tab {{ request('status') === null || request('status') === 'S' ? 'active' : '' }}">
            <i class="bi bi-clock me-2"></i>Pending
        </a>
        <a href="{{ route('backend.payment-note.index', ['status' => 'A']) }}" 
           class="modern-tab {{ request('status') === 'A' ? 'active' : '' }}">
            <i class="bi bi-check-circle me-2"></i>Approved
        </a>
        <a href="{{ route('backend.payment-note.index', ['status' => 'R']) }}" 
           class="modern-tab {{ request('status') === 'R' ? 'active' : '' }}">
            <i class="bi bi-x-circle me-2"></i>Rejected
        </a>
        <a href="{{ route('backend.payment-note.index', ['status' => 'D']) }}" 
           class="modern-tab {{ request('status') === 'D' ? 'active' : '' }}">
            <i class="bi bi-file-earmark me-2"></i>Draft
        </a>
    </div>

    <!-- Bulk Bank Letter Form -->
    <form id="bulkBankLetterForm" action="{{ route('backend.payments.createBankLetter') }}" method="POST">
        @csrf
        <input type="hidden" name="note_ids" id="selectedNoteIds">
    </form>

    <div class="modern-content">
        <!-- Modern Data Table Card -->
        <div class="modern-card">
            <div class="modern-card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 text-gray-900">
                            <i class="bi bi-table text-primary me-2"></i>Payment Notes
                        </h3>
                        @if(request('status'))
                            <span class="modern-badge modern-badge-info">
                                {{ ucfirst(request('status')) }} Status
                            </span>
                        @endif
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        @php $userRoles = auth()->user()->getRoleNames(); @endphp
                        @if($userRoles->contains('PN User'))
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="submitSelectedNotes()">
                                <i class="bi bi-file-earmark-check me-1"></i>Create Bank Letter
                            </button>
                        @endif
                        <div class="modern-search">
                            <i class="bi bi-search modern-search-icon"></i>
                            <input type="text" class="modern-input modern-search-input" placeholder="Search notes...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modern-card-body p-0">
                <!-- Modern DataTable -->
                <div class="table-responsive">
                    <table class="modern-table datatable" id="payment_notes_index_dt" style="width: 100%;">
                        <thead>
                            <tr>
                                <th width="3%">Select</th>
                                <th width="5%">#</th>
                                <th width="18%">Project</th>
                                <th width="18%">Vendor/Employee</th>
                                <th width="12%">Amount</th>
                                <th width="12%">Date</th>
                                <th width="20%">Status</th>
                                <th width="12%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notes as $index => $note)
                                <tr>
                                    <td>
                                        @if ($userRoles->contains('PN User') && $note->status == 'A')
                                            <input type="checkbox" class="note-checkbox form-check-input" value="{{ $note->id }}">
                                        @endif
                                    </td>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if ($note->greenNote)
                                            <strong>{{ $note->greenNote->vendor->project ?? '-' }}</strong>
                                        @elseif ($note->reimbursementNote)
                                            <strong>{{ $note->reimbursementNote->project->project ?? '-' }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($note->greenNote)
                                            <strong>{{ $note->greenNote->supplier->vendor_name ?? '-' }}</strong>
                                        @elseif ($note->reimbursementNote)
                                            <strong>{{ $note->reimbursementNote->selectUser ? $note->reimbursementNote->selectUser->name : $note->reimbursementNote->user->name }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-success">{{ \App\Helpers\Helper::formatIndianNumber($note->net_payable_round_off) ?? '-' }}</strong>
                                    </td>
                                    <td>
                                        <small>{{ $note->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $statusLabels = [
                                                'D' => '<span class="badge bg-dark">Draft</span>',
                                                'P' => '<span class="badge bg-warning">Pending</span>',
                                                'A' => '<span class="badge bg-success">Approved</span>',
                                                'R' => '<span class="badge bg-danger">Rejected</span>',
                                                'S' => '<span class="badge bg-secondary">Sent for Approval</span>',
                                                'B' => '<span class="badge bg-dark">RTGS/NEFT Created</span>',
                                                'PA' => '<span class="badge bg-dark">Payment Approved</span>',
                                                'PD' => '<span class="badge bg-info">Paid</span>',
                                            ];
                                        @endphp
                                        {!! $statusLabels[$note->status] ?? '-' !!}
                                        
                                        @if ($note->status == 'PD' && $note->utr_date)
                                            <div class="text-muted mt-1" style="font-size: 11px;">
                                                <strong>UTR Date:</strong> {{ \Carbon\Carbon::parse($note->utr_date)->format('d-m-Y') ?? '-' }}<br>
                                                <strong>UTR Number:</strong> {{ $note->utr_no ?? '-' }}
                                            </div>
                                        @endif
                                        
                                        @if (!in_array($note->status, ['A', 'B', 'PA', 'PD']))
                                            @php
                                                $nextApprovers = collect();
                                                $pendingLogs = $note->paymentApprovalLogs->where('status', 'P');
                                                
                                                if ($pendingLogs->isNotEmpty()) {
                                                    $lastPendingLog = $pendingLogs->last();
                                                    foreach ($lastPendingLog->logPriorities as $logPriority) {
                                                        if ($logPriority->priority && $logPriority->priority->user) {
                                                            $nextApprovers->push($logPriority->priority->user);
                                                        }
                                                    }
                                                }
                                                
                                                if ($nextApprovers->isEmpty()) {
                                                    $approvalStep = \App\Models\PaymentNoteApprovalStep::where('min_amount', '<=', $note->net_payable_round_off)
                                                        ->where(function ($query) use ($note) {
                                                            $query->where('max_amount', '>=', $note->net_payable_round_off)
                                                                  ->orWhereNull('max_amount');
                                                        })
                                                        ->orderBy('min_amount', 'desc')
                                                        ->with('approvers.user')
                                                        ->first();
                                                        
                                                    if ($approvalStep) {
                                                        $approvedLogsCount = $note->paymentApprovalLogs->where('status', 'A')->count();
                                                        $currentLevel = $approvedLogsCount + 1;
                                                        $currentLevelApprovers = $approvalStep->approvers->where('approver_level', $currentLevel);
                                                        
                                                        if ($currentLevelApprovers->isNotEmpty()) {
                                                            foreach ($currentLevelApprovers as $approver) {
                                                                if ($approver->user) {
                                                                    $nextApprovers->push($approver->user);
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            @endphp
                                            
                                            @if ($nextApprovers->isNotEmpty())
                                                <div class="mt-1">
                                                    <small class="text-muted">Next Approver:</small><br>
                                                    <small class="text-primary">
                                                        @foreach ($nextApprovers as $index => $approver)
                                                            {{ $approver->name }}@if ($index < $nextApprovers->count() - 1), @endif
                                                        @endforeach
                                                    </small>
                                                </div>
                                            @else
                                                <div class="mt-1">
                                                    <small class="text-muted">
                                                        @if ($note->paymentApprovalLogs->where('status', 'A')->count() > 0)
                                                            All configured approvers have approved
                                                        @else
                                                            No approvers configured
                                                        @endif
                                                    </small>
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @if ($note->canBeEditedBy(auth()->id()))
                                                <a href="{{ route('backend.payment-note.edit', $note->id) }}" 
                                                   class="btn btn-outline-primary btn-sm" 
                                                   title="Edit Payment Note">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            @endif
                                            
                                            <a href="{{ route('backend.payment-note.show', $note->id) }}"
                                               class="btn btn-outline-info btn-sm"
                                               title="View Payment Note">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            @if (in_array($note->status, ['A', 'B', 'PA']) && $userRoles->contains('PN User'))
                                                <button class="btn btn-outline-warning btn-sm open-update-modal"
                                                        data-id="{{ $note->id }}"
                                                        title="Update UTR Details">
                                                    <i class="bi bi-calendar2-plus"></i>
                                                </button>
                                            @endif
                                            
                                            @can(['delete-payment-note'])
                                                <form action="{{ route('backend.payment-note.destroy', $note->id) }}"
                                                      method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                                            onclick="return confirm('Are you sure?')"
                                                            title="Delete Payment Note">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> <!-- Close modern-content -->
</div> <!-- Close modern-container -->

    <!-- Update UTR and Date Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="updateForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between">
                        <h5 class="modal-title">Update Date & UTR No</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="note_id" id="modal_note_id">

                        <div class="mb-3">
                            <label for="utr_no" class="form-label">UTR No</label>
                            <input type="text" class="form-control" name="utr_no" id="utr_no" required>
                        </div>

                        <div class="mb-3">
                            <label for="utr_date" class="form-label">Date</label>
                            <input type="date" class="form-control" name="utr_date" id="utr_date" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="fullscreenLoader" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.8); z-index: 9999; justify-content: center; align-items: center;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
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

    .modern-tabs {
        background: white;
        border-radius: 1rem;
        padding: 0.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        display: flex;
        gap: 0.25rem;
        flex-wrap: wrap;
    }

    .modern-tab {
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        text-decoration: none;
        color: #6c757d;
        font-weight: 500;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        white-space: nowrap;
    }

    .modern-tab:hover {
        background: #f8f9fa;
        color: #007bff;
        transform: translateY(-1px);
    }

    .modern-tab.active {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        box-shadow: 0 4px 15px rgba(0,123,255,0.3);
    }

    .modern-content {
        margin-bottom: 2rem;
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
        padding: 0;
    }

    .modern-search {
        position: relative;
        width: 300px;
    }

    .modern-search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 2;
    }

    .modern-search-input {
        padding-left: 2.5rem !important;
        border-radius: 2rem !important;
        border: 2px solid #e9ecef !important;
        transition: all 0.3s ease !important;
    }

    .modern-search-input:focus {
        border-color: #007bff !important;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25) !important;
    }

    .modern-input {
        border: 2px solid #e9ecef;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .modern-badge {
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .modern-badge-info {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
    }

    .modern-table {
        margin-bottom: 0 !important;
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        color: #495057;
        padding: 1rem 0.75rem;
    }

    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
        color: #212529;
    }

    .table-responsive {
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(253, 126, 20, 0.15) !important;
    }

    /* Status badges with proper colors */
    .badge-pending {
        background-color: #fff3cd !important;
        color: #856404 !important;
        border: 1px solid #856404;
    }

    .badge-approved {
        background-color: #d4edda !important;
        color: #155724 !important;
        border: 1px solid #155724;
    }

    .badge-rejected {
        background-color: #ffebe9 !important;
        color: #dc3545 !important;
        border: 1px solid #dc3545;
    }

    .badge-draft {
        background-color: #e9ecef !important;
        color: #495057 !important;
        border: 1px solid #495057;
    }

    /* Action buttons with proper styling */
    .btn-action {
        margin-right: 0.25rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .btn-outline-primary:hover {
        background-color: #198754;
        border-color: #198754;
    }

    .btn-outline-info:hover {
        background-color: #fd7e14;
        border-color: #fd7e14;
    }

    .btn-outline-warning:hover {
        background-color: #ffc107;
        border-color: #ffc107;
    }

    .btn-outline-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    /* Tab styling */
    .nav-tabs {
        border-bottom: 2px solid #e9ecef;
    }

    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 500;
    }

    .nav-tabs .nav-link.active {
        color: #198754;
        background-color: transparent;
        border-bottom: 3px solid #198754;
    }

    .nav-tabs .nav-link:hover {
        border-color: transparent;
        color: #198754;
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

    /* Loading spinner */
    .spinner-border-sm {
        color: #198754;
    }

    /* Form styling */
    .form-label {
        font-weight: 500;
        color: #495057;
    }

    .form-control {
        border-radius: 0.375rem;
    }
</style>
@endpush

@push('script')
    <script>
        $(function () {
            // Initialize DataTable with enhanced functionality
            var dt = $('#payment_notes_index_dt').DataTable({
                processing: false, // Using server-side data, no need for processing
                serverSide: false, // Data is already loaded from server
                responsive: true,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                order: [[1, 'desc']], // Order by index column descending
                columnDefs: [
                    {
                        targets: [0, 7], // Select and Actions columns
                        orderable: false,
                        searchable: false
                    },
                    {
                        targets: [4], // Amount column
                        type: 'num-fmt' // For proper number sorting
                    }
                ],
                language: {
                    emptyTable: 'No payment notes found',
                    zeroRecords: 'No matching payment notes found',
                    info: 'Showing _START_ to _END_ of _TOTAL_ payment notes',
                    infoEmpty: 'Showing 0 to 0 of 0 payment notes',
                    infoFiltered: '(filtered from _MAX_ total payment notes)'
                },
                initComplete: function() {
                    // Add search functionality styling
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                    
                    // Add select all functionality
                    $('#selectAllNotes').on('change', function() {
                        $('.note-checkbox').prop('checked', this.checked);
                    });
                }
            });
            
            // Custom search functionality
            $('.modern-search-input').on('keyup', function() {
                dt.search(this.value).draw();
            });
        });

        $(document).on('click', '.open-update-modal', function() {
            let id = $(this).data('id');
            $('#modal_note_id').val(id);
            $('#utr_no').val('');
            $('#utr_date').val('');
            $('#updateModal').modal('show');
        });

        $('#updateForm').submit(function(e) {
            e.preventDefault();

            $('#fullscreenLoader').css('display', 'flex');
            $.ajax({
                url: "{{ route('backend.payment-note.updateUtr') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#fullscreenLoader').hide();
                        $('#updateModal').modal('hide');
                        alert('Updated successfully!');
                        location.reload();
                    } else {
                        $('#fullscreenLoader').hide();
                        $('#updateModal').modal('hide');
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    $('#fullscreenLoader').hide();
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            let input = $('[name=' + field + ']');
                            input.after('<div class="text-danger">' + messages[0] + '</div>');
                        });
                    } else {
                        alert('Something went wrong!');
                    }
                }
            });
        });

        function submitSelectedNotes() {
            const selectedCheckboxes = document.querySelectorAll('.note-checkbox:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (selectedIds.length === 0) {
                // Modern notification instead of alert
                showNotification('Please select at least one note.', 'warning');
                return;
            }

            // Show confirmation modal
            if (confirm(`Are you sure you want to create bank letter for ${selectedIds.length} selected payment note(s)?`)) {
                document.getElementById('selectedNoteIds').value = selectedIds.join(',');
                document.getElementById('bulkBankLetterForm').submit();
            }
        }
        
        // Modern notification function
        function showNotification(message, type = 'info') {
            const notification = $(`
                <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
                     style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
            
            $('body').append(notification);
            
            // Auto dismiss after 5 seconds
            setTimeout(() => {
                notification.alert('close');
            }, 5000);
        }
    </script>
@endpush
