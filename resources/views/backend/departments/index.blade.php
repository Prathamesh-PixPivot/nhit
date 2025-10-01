@extends('backend.layouts.app')

@section('title', 'Departments Management')

@section('content')
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-success mb-1">
                        <i class="bi bi-building me-2"></i>Departments Management
                    </h2>
                    <p class="text-muted mb-0">Organize and manage organizational departments</p>
                </div>
                <div class="d-flex gap-2">
                    @can('create-department')
                        <a href="{{ route('backend.departments.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i>Add New Department
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
                    <li class="breadcrumb-item active" aria-current="page">Departments</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-table text-success me-2"></i>All Departments
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Departments DataTable -->
                    <div class="table-responsive">
                        <table class="table table-hover datatable" id="departments_dt" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%">#</th>
                                    <th width="35%">Name</th>
                                    <th width="40%">Description</th>
                                    <th width="15%">Actions</th>
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

    /* Action buttons */
    .btn-action {
        margin-right: 0.25rem;
        border-radius: 0.375rem;
    }

    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    /* Description text */
    .text-muted {
        color: #6c757d !important;
    }
</style>
@endpush

@push('script')
    <script>
        $(function(){
            $('#departments_dt').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                deferRender: true,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                ajax: {
                    url: '{{ route('backend.departments.index') }}'
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '10%'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        width: '35%'
                    },
                    {
                        data: 'description',
                        name: 'description',
                        width: '40%',
                        render: function(data, type, row) {
                            return data ? data : '<span class="text-muted">No description</span>';
                        }
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    }
                ],
                language: {
                    processing: '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div> Processing...',
                    emptyTable: 'No departments found',
                    zeroRecords: 'No matching departments found'
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
