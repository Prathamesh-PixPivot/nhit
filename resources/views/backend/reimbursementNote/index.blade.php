@extends('backend.layouts.app')

@section('title', 'Reimbursement Notes Management')

@section('content')
<div class="modern-container">
    <!-- Modern Header -->
    <div class="modern-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="modern-page-title">
                    <i class="bi bi-wallet text-primary me-3"></i>Reimbursement Notes
                </h1>
                <p class="modern-page-subtitle">Manage employee reimbursement requests and approvals</p>
            </div>
            <div class="d-flex gap-3">
                @can('create-reimbursement-note')
                    <a href="{{ route('backend.reimbursement-note.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Create Reimbursement
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
        <span>Reimbursement Notes</span>
    </div>

    <!-- Modern Status Tabs -->
    <div class="modern-tabs">
        <a href="{{ route('backend.reimbursement-note.index', ['status' => 'all']) }}" 
           class="modern-tab {{ request('status') === 'all' ? 'active' : '' }}">
            <i class="bi bi-list-ul me-2"></i>All Notes
        </a>
        <a href="{{ route('backend.reimbursement-note.index', ['status' => 'S']) }}" 
           class="modern-tab {{ request('status') === null || request('status') === 'S' ? 'active' : '' }}">
            <i class="bi bi-clock me-2"></i>Pending
        </a>
        <a href="{{ route('backend.reimbursement-note.index', ['status' => 'A']) }}" 
           class="modern-tab {{ request('status') === 'A' ? 'active' : '' }}">
            <i class="bi bi-check-circle me-2"></i>Approved
        </a>
        <a href="{{ route('backend.reimbursement-note.index', ['status' => 'R']) }}" 
           class="modern-tab {{ request('status') === 'R' ? 'active' : '' }}">
            <i class="bi bi-x-circle me-2"></i>Rejected
        </a>
        <a href="{{ route('backend.reimbursement-note.index', ['status' => 'D']) }}" 
           class="modern-tab {{ request('status') === 'D' ? 'active' : '' }}">
            <i class="bi bi-file-earmark me-2"></i>Draft
        </a>
    </div>

    <div class="modern-content">
        <!-- Modern Data Table Card -->
        <div class="modern-card">
            <div class="modern-card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 text-gray-900">
                            <i class="bi bi-table text-primary me-2"></i>Reimbursement Notes
                        </h3>
                        @if(request('status'))
                            <span class="modern-badge modern-badge-info">
                                {{ ucfirst(request('status')) }} Status
                            </span>
                        @endif
                    </div>
                    <div class="modern-search">
                        <i class="bi bi-search modern-search-icon"></i>
                        <input type="text" class="modern-input modern-search-input" placeholder="Search notes...">
                    </div>
                </div>
            </div>
            <div class="modern-card-body p-0">
                <!-- Modern DataTable -->
                <div class="table-responsive">
                    <table class="modern-table datatable" id="reimbursement_notes_index_dt" style="width: 100%;">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="20%">Project</th>
                                <th width="20%">Employee</th>
                                <th width="15%">Amount</th>
                                <th width="15%">Date</th>
                                <th width="15%">Status</th>
                                <th width="10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> <!-- Close modern-content -->
</div> <!-- Close modern-container -->
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
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }

    /* Status badges with proper colors */
    .badge-pending {
        background-color: #fff3cd !important;
        color: #856404 !important;
        border: 1px solid #856404;
    }

    .badge-approved {
        background-color: #d1edff !important;
        color: #0c63e4 !important;
        border: 1px solid #0c63e4;
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
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .btn-outline-info:hover {
        background-color: #0dcaf0;
        border-color: #0dcaf0;
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
        color: #0d6efd;
        background-color: transparent;
        border-bottom: 3px solid #0d6efd;
    }

    .nav-tabs .nav-link:hover {
        border-color: transparent;
        color: #0d6efd;
    }

    /* DataTables styling */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    .dataTables_wrapper .dataTables_length select:focus,
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    /* Loading spinner */
    .spinner-border-sm {
        color: #0d6efd;
    }
</style>
@endpush

@push('script')
    <script>
        $(function(){
            $('#reimbursement_index_dt').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                deferRender: true,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                ajax: {
                    url: '{{ route('backend.reimbursement-note.index') }}',
                    data: function(d){
                        d.status = '{{ request('status') }}';
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '5%'
                    },
                    {
                        data: 'project_name',
                        name: 'project_name',
                        width: '20%'
                    },
                    {
                        data: 'employee_name',
                        name: 'employee_name',
                        width: '20%',
                        render: function(data, type, row) {
                            if (!data || data === '-' || data === 'null' || data === '' || data === 'N/A') {
                                return '<span class="text-muted">N/A</span>';
                            }
                            return '<strong>' + data + '</strong>';
                        }
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        width: '15%',
                        render: function(data, type, row) {
                            // Handle various data formats
                            let amount = 0;
                            
                            if (data !== null && data !== undefined && data !== '' && data !== 'null') {
                                // Remove any currency symbols and commas
                                let cleanData = String(data).replace(/[₹,]/g, '').trim();
                                amount = parseFloat(cleanData);
                            }
                            
                            // Check if amount is valid
                            if (isNaN(amount) || amount === 0) {
                                return '<span class="text-muted">₹0.00</span>';
                            }
                            
                            // Format with Indian number system
                            return '<strong class="text-success">₹' + amount.toLocaleString('en-IN', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }) + '</strong>';
                        }
                    },
                    {
                        data: 'date',
                        name: 'created_at',
                        width: '15%'
                    },
                    {
                        data: 'status_badge',
                        name: 'status',
                        width: '15%',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            // Handle different status formats
                            if (data && data.includes('badge')) {
                                return data; // Already formatted badge
                            }
                            var statusLabels = {
                                'S': 'Pending',
                                'A': 'Approved',
                                'R': 'Rejected',
                                'D': 'Draft'
                            };
                            var statusClasses = {
                                'S': 'badge-pending',
                                'A': 'badge-approved',
                                'R': 'badge-rejected',
                                'D': 'badge-draft'
                            };
                            var status = row.status || 'S';
                            return '<span class="badge ' + (statusClasses[status] || 'badge-secondary') + '">' +
                                   (statusLabels[status] || status) + '</span>';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '10%'
                    }
                ],
                language: {
                    processing: '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div> Processing...',
                    emptyTable: 'No reimbursement notes found',
                    zeroRecords: 'No matching reimbursement notes found'
                },
                initComplete: function() {
                    // Add search functionality styling
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
            });
        });
    </script>
@endpush
