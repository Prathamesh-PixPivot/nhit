@extends('backend.layouts.app')

@section('title', 'Reimbursement Notes Management')

@section('content')
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="bi bi-receipt-cutoff me-2"></i>Reimbursement Notes Management
                    </h2>
                    <p class="text-muted mb-0">Manage employee reimbursement requests and approvals</p>
                </div>
                <div class="d-flex gap-2">
                    @can('create-reimbursement-note')
                        <a href="{{ route('backend.reimbursement-note.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Create Reimbursement
                        </a>
                    @endcan
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
                    <li class="breadcrumb-item active" aria-current="page">Reimbursement Notes</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <ul class="nav nav-tabs" id="reimbursementTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request('status') === 'all' ? 'active' : '' }}"
                               href="{{ route('backend.reimbursement-note.index', ['status' => 'all']) }}" role="tab">
                                <i class="bi bi-list-ul me-1"></i>All
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request('status') === null || request('status') === 'S' ? 'active' : '' }}"
                               href="{{ route('backend.reimbursement-note.index', ['status' => 'S']) }}" role="tab">
                                <i class="bi bi-clock me-1"></i>Pending
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request('status') === 'A' ? 'active' : '' }}"
                               href="{{ route('backend.reimbursement-note.index', ['status' => 'A']) }}" role="tab">
                                <i class="bi bi-check-circle me-1"></i>Approved
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request('status') === 'R' ? 'active' : '' }}"
                               href="{{ route('backend.reimbursement-note.index', ['status' => 'R']) }}" role="tab">
                                <i class="bi bi-x-circle me-1"></i>Rejected
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request('status') === 'D' ? 'active' : '' }}"
                               href="{{ route('backend.reimbursement-note.index', ['status' => 'D']) }}" role="tab">
                                <i class="bi bi-file-earmark me-1"></i>Draft
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-table text-primary me-2"></i>Reimbursement Requests
                        @if(request('status'))
                            <span class="badge bg-primary ms-2">{{ ucfirst(request('status')) }}</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Reimbursement Notes DataTable -->
                    <div class="table-responsive">
                        <table class="table table-hover datatable" id="reimbursement_index_dt" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="20%">Project Name</th>
                                    <th width="20%">Employee Name</th>
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
                        width: '20%'
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        width: '15%',
                        render: function(data, type, row) {
                            return '₹' + parseFloat(data || 0).toLocaleString('en-IN');
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
