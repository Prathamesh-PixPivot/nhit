@extends('backend.layouts.app')

@section('title', 'Expense Notes Management')

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
                    <i class="bi bi-receipt text-primary me-3"></i>Expense Notes
                </h1>
                <p class="modern-page-subtitle">Manage and track expense note requests and approvals</p>
            </div>
            <div class="d-flex gap-3">
                @can('create-note')
                    <a href="{{ route('backend.note.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Create Expense Note
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
        <span>Expense Notes</span>
    </div>

    <!-- Modern Status Tabs -->
    <div class="modern-tabs">
        <a href="{{ route('backend.note.index', ['status' => 'all']) }}" 
           class="modern-tab {{ request('status') === 'all' ? 'active' : '' }}">
            <i class="bi bi-list-ul me-2"></i>All Notes
        </a>
        <a href="{{ route('backend.note.index', ['status' => 'S']) }}" 
           class="modern-tab {{ request('status') === null || request('status') === 'S' ? 'active' : '' }}">
            <i class="bi bi-clock me-2"></i>Pending
        </a>
        <a href="{{ route('backend.note.index', ['status' => 'A']) }}" 
           class="modern-tab {{ request('status') === 'A' ? 'active' : '' }}">
            <i class="bi bi-check-circle me-2"></i>Approved
        </a>
        <a href="{{ route('backend.note.index', ['status' => 'R']) }}" 
           class="modern-tab {{ request('status') === 'R' ? 'active' : '' }}">
            <i class="bi bi-x-circle me-2"></i>Rejected
        </a>
        <a href="{{ route('backend.note.index', ['status' => 'D']) }}" 
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
                        <i class="bi bi-table text-primary me-2"></i>Expense Notes
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
                <table class="modern-table datatable" id="green_notes_index_dt" style="width: 100%;">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="20%">Project</th>
                            <th width="20%">Vendor</th>
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


@push('script')
    <script>
        $(function(){
            $('#green_notes_index_dt').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                deferRender: true,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                ajax: {
                    url: '{{ route('backend.note.index') }}',
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
                        data: 'vendor_name',
                        name: 'vendor_name',
                        width: '20%',
                        render: function(data, type, row) {
                            if (!data || data === '-' || data === 'null' || data === '') {
                                return '<span class="text-muted">N/A</span>';
                            }
                            return '<strong>' + data + '</strong>';
                        }
                    },
                    {
                        data: 'invoice_value',
                        name: 'invoice_value',
                        width: '15%',
                        render: function(data, type, row) {
                            // If data is already formatted by backend, just add currency symbol
                            if (data && data !== '-' && data !== 'null' && data !== '' && data !== '0') {
                                // Check if it's already formatted (contains commas)
                                if (String(data).includes(',')) {
                                    return '<strong class="text-success">₹' + data + '</strong>';
                                }
                                
                                // Otherwise parse and format
                                let cleanData = String(data).replace(/[₹,]/g, '').trim();
                                let amount = parseFloat(cleanData);
                                
                                if (!isNaN(amount) && amount > 0) {
                                    return '<strong class="text-success">₹' + amount.toLocaleString('en-IN', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }) + '</strong>';
                                }
                            }
                            
                            return '<span class="text-muted">₹0.00</span>';
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
                            if (data && data.includes('modern-badge')) {
                                return data; // Already formatted badge
                            }
                            var statusLabels = {
                                'S': 'Pending',
                                'A': 'Approved', 
                                'R': 'Rejected',
                                'D': 'Draft',
                                'H': 'On Hold'
                            };
                            var statusClasses = {
                                'S': 'modern-badge-warning',
                                'A': 'modern-badge-success',
                                'R': 'modern-badge-error',
                                'D': 'modern-badge-secondary',
                                'H': 'modern-badge-info'
                            };
                            var status = row.status || 'S';
                            return '<span class="modern-badge ' + (statusClasses[status] || 'modern-badge-secondary') + '">' +
                                   '<i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>' +
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
                    emptyTable: 'No green notes found',
                    zeroRecords: 'No matching green notes found'
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
