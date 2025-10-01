@extends('backend.layouts.app')

@section('title', 'Vendors Management')

@section('content')
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-success mb-1">
                        <i class="bi bi-building me-2"></i>Vendors Management
                    </h2>
                    <p class="text-muted mb-0">Manage vendor information and banking details</p>
                </div>
                <div class="d-flex gap-2">
                    @can('create-vendor')
                        <a href="{{ route('backend.vendors.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i>Add New Vendor
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
                    <li class="breadcrumb-item active" aria-current="page">Vendors</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-table text-success me-2"></i>All Vendors
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Vendors DataTable -->
                    <div class="table-responsive">
                        <table class="table table-hover datatable" id="vendors_datatable" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="10%">Code</th>
                                    <th width="20%">Name</th>
                                    <th width="20%">Email</th>
                                    <th width="15%">Mobile</th>
                                    <th width="15%">Beneficiary</th>
                                    <th width="10%">Status</th>
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
    .badge-active {
        background-color: #d1edff;
        color: #0c63e4;
    }

    .badge-inactive {
        background-color: #ffebe9;
        color: #dc3545;
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

    /* Truncate long text */
    .text-truncate {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
@endpush

@push('script')
    <script type="text/javascript">
        $(function() {
            var table = $('#vendors_datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('backend.vendors.index') }}",
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
                        data: 'vendor_code',
                        name: 'vendor_code',
                        width: '10%'
                    },
                    {
                        data: 'vendor_name',
                        name: 'vendor_name',
                        width: '20%'
                    },
                    {
                        data: 'vendor_email',
                        name: 'vendor_email',
                        width: '20%'
                    },
                    {
                        data: 'vendor_mobile',
                        name: 'vendor_mobile',
                        width: '15%'
                    },
                    {
                        data: 'benificiary_name',
                        name: 'benificiary_name',
                        width: '15%'
                    },
                    {
                        data: 'active',
                        name: 'active',
                        width: '10%',
                        render: function(data, type, row) {
                            return data === 'Active' ?
                                '<span class="badge badge-active">Active</span>' :
                                '<span class="badge badge-inactive">Inactive</span>';
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
                    emptyTable: 'No vendors found',
                    zeroRecords: 'No matching vendors found'
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
