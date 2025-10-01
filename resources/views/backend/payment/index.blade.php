@extends('backend.layouts.app')

@section('title', 'Payments Management')

@section('content')
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-success mb-1">
                        <i class="bi bi-credit-card me-2"></i>Payments Management
                    </h2>
                    <p class="text-muted mb-0">Manage bank RTGS/NEFT payment requests</p>
                </div>
                <div class="d-flex gap-2">
                    @can('create-payment')
                        <a href="{{ route('backend.payments.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i>Create Payment
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
                    <li class="breadcrumb-item active" aria-current="page">Payments</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <ul class="nav nav-tabs" id="paymentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request('status') === 'all' ? 'active' : '' }}"
                               href="{{ route('backend.payments.index', ['status' => 'all']) }}" role="tab">
                                <i class="bi bi-list-ul me-1"></i>All
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request('status') === null || request('status') === 'S' ? 'active' : '' }}"
                               href="{{ route('backend.payments.index', ['status' => 'S']) }}" role="tab">
                                <i class="bi bi-clock me-1"></i>Pending
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request('status') === 'A' ? 'active' : '' }}"
                               href="{{ route('backend.payments.index', ['status' => 'A']) }}" role="tab">
                                <i class="bi bi-check-circle me-1"></i>Approved
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request('status') === 'R' ? 'active' : '' }}"
                               href="{{ route('backend.payments.index', ['status' => 'R']) }}" role="tab">
                                <i class="bi bi-x-circle me-1"></i>Rejected
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ request('status') === 'D' ? 'active' : '' }}"
                               href="{{ route('backend.payments.index', ['status' => 'D']) }}" role="tab">
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
                        <i class="bi bi-table text-success me-2"></i>Payment Requests
                        @if(request('status'))
                            <span class="badge bg-primary ms-2">{{ ucfirst(request('status')) }}</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Payments DataTable -->
                    <div class="table-responsive">
                        <table class="table table-hover datatable" id="payment_datatable" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">SL No.</th>
                                    <th width="20%">Vendor Name</th>
                                    <th width="15%">Amount</th>
                                    <th width="15%">Date</th>
                                    <th width="15%">Status</th>
                                    <th width="10%">Shortcut</th>
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
        background-color: #fff3cd;
        color: #856404;
    }

    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
    }

    .table-responsive {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }

    /* Status badges */
    .badge-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .badge-approved {
        background-color: #d1edff;
        color: #0c63e4;
    }

    .badge-rejected {
        background-color: #ffebe9;
        color: #dc3545;
    }

    .badge-draft {
        background-color: #e9ecef;
        color: #495057;
    }

    /* Action buttons */
    .btn-action {
        margin-right: 0.25rem;
        border-radius: 0.375rem;
    }

    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
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
</style>
@endpush

@push('script')
    <script type="text/javascript">
        $(function() {
            var table = $('#payment_datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('backend.payments.index') }}',
                    data: function(d) {
                        d.status = '{{ request()->get('status') }}';
                    }
                },
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '5%'
                    },
                    {
                        data: 'sl_no',
                        name: 'sl_no',
                        width: '10%'
                    },
                    {
                        data: 'vendor_name',
                        name: 'vendor_name',
                        width: '20%'
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        width: '15%',
                        render: function(data, type, row) {
                            return '₹' + parseFloat(data).toLocaleString('en-IN');
                        }
                    },
                    {
                        data: 'date',
                        name: 'date',
                        width: '15%'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        width: '15%',
                        render: function(data, type, row) {
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
                            return '<span class="badge ' + (statusClasses[data] || 'badge-secondary') + '">' +
                                   (statusLabels[data] || data) + '</span>';
                        }
                    },
                    {
                        data: 'shortcut_name',
                        name: 'shortcut_name',
                        width: '10%',
                        render: function(data, type, row) {
                            return data ? '<span class="badge bg-info">' + data + '</span>' : '-';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '10%'
                    },
                ],
                language: {
                    processing: '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div> Processing...',
                    emptyTable: 'No payments found',
                    zeroRecords: 'No matching payments found'
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
