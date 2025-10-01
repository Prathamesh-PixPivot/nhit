@extends('backend.layouts.app')

@section('title', 'Users Management')

@section('content')
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-success mb-1">
                        <i class="bi bi-people me-2"></i>Users Management
                    </h2>
                    <p class="text-muted mb-0">Manage system users, roles, and permissions</p>
                </div>
                <div class="d-flex gap-2">
                    @can('create-user')
                        <a href="{{ route('backend.users.create') }}" class="btn btn-success">
                            <i class="bi bi-person-plus me-1"></i>Add New User
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
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-table text-success me-2"></i>All Users
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Users DataTable -->
                    <div class="table-responsive">
                        <table class="table table-hover datatable" id="users_datatable" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="20%">Name</th>
                                    <th width="15%">Username</th>
                                    <th width="25%">Email</th>
                                    <th width="20%">Roles</th>
                                    <th width="10%">Status</th>
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
        color: #212529;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
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
    .badge-active {
        background-color: #d4edda !important;
        color: #155724 !important;
        border: 1px solid #155724;
    }

    .badge-inactive {
        background-color: #ffebe9 !important;
        color: #dc3545 !important;
        border: 1px solid #dc3545;
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

    /* DataTables styling */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }

    .dataTables_wrapper .dataTables_length select:focus,
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
    }

    /* Loading spinner */
    .spinner-border-sm {
        color: #198754;
    }

    /* Empty state styling */
    .dataTables_empty {
        text-align: center;
        padding: 3rem;
        color: #6c757d;
        font-style: italic;
    }
</style>
@endpush

@push('script')
    <script type="text/javascript">
        $(function() {
            // Prevent conflicts with other DataTable libraries
            if (typeof $.fn.DataTable !== 'undefined') {
                $.fn.DataTable.ext.errMode = 'none'; // Disable error alerts
            }
            
            // Initialize DataTable with optimized settings for large datasets
            try {
                var table = $('#users_datatable').DataTable({
                    processing: true,
                    serverSide: true, // Re-enabled for large datasets
                    responsive: true,
                    deferRender: true, // Only render visible rows
                    ajax: {
                        url: "{{ route('backend.users.index') }}",
                        type: 'GET',
                        error: function(xhr, error, code) {
                            console.log('Users data could not be loaded:', error);
                            // Fallback to empty state
                            if (table && typeof table.clear === 'function') {
                                table.clear().draw();
                            }
                        }
                    },
                    pageLength: 50, // Increased for better performance
                    lengthMenu: [[25, 50, 100, 200], [25, 50, 100, 200]],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            width: '5%',
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: 'name',
                            name: 'name',
                            width: '20%',
                            render: function(data, type, row) {
                                if (type === 'display' && data) {
                                    return '<div class="text-truncate" title="' + data + '">' + data + '</div>';
                                }
                                return data || '-';
                            }
                        },
                        {
                            data: 'username',
                            name: 'username',
                            width: '15%',
                            render: function(data, type, row) {
                                if (type === 'display' && data) {
                                    return '<code class="text-muted">' + data + '</code>';
                                }
                                return data || '-';
                            }
                        },
                        {
                            data: 'email',
                            name: 'email',
                            width: '25%',
                            render: function(data, type, row) {
                                if (type === 'display' && data) {
                                    return '<div class="text-truncate" title="' + data + '">' + data + '</div>';
                                }
                                return data || '-';
                            }
                        },
                        {
                            data: 'roles',
                            name: 'roles',
                            width: '20%',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                if (type === 'display') {
                                    // Handle different data types safely
                                    let rolesArray = [];
                                    
                                    try {
                                        if (Array.isArray(data)) {
                                            rolesArray = data;
                                        } else if (typeof data === 'string') {
                                            // Handle comma-separated string
                                            rolesArray = data.split(',').map(role => role.trim()).filter(role => role);
                                        } else if (data && typeof data === 'object') {
                                            // Handle object with roles property
                                            rolesArray = data.roles || [];
                                        }
                                        
                                        if (rolesArray.length > 0) {
                                            // Limit displayed roles for performance
                                            const maxRoles = 3;
                                            const displayRoles = rolesArray.slice(0, maxRoles);
                                            let html = displayRoles.map(role =>
                                                '<span class="badge bg-warning text-dark me-1 mb-1">' + role + '</span>'
                                            ).join('');
                                            
                                            if (rolesArray.length > maxRoles) {
                                                html += '<span class="badge bg-secondary text-white">+' + (rolesArray.length - maxRoles) + '</span>';
                                            }
                                            return html;
                                        }
                                    } catch (e) {
                                        console.log('Error processing roles data:', e);
                                    }
                                    return '<span class="text-muted">No roles</span>';
                                }
                                return data;
                            }
                        },
                        {
                            data: 'active',
                            name: 'active',
                            width: '10%',
                            render: function(data, type, row) {
                                if (type === 'display') {
                                    return data === 'Y' || data === true ?
                                        '<span class="badge badge-active">Active</span>' :
                                        '<span class="badge badge-inactive">Inactive</span>';
                                }
                                return data;
                            }
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            width: '15%',
                            render: function(data, type, row) {
                                if (type === 'display') {
                                    return data || '<span class="text-muted">-</span>';
                                }
                                return data;
                            }
                        },
                    ],
                    order: [[1, 'asc']], // Default sort by name
                    language: {
                        processing: '<div class="d-flex align-items-center justify-content-center p-3"><div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>Loading users...</div>',
                        emptyTable: 'No users available',
                        zeroRecords: 'No matching users found',
                        info: 'Showing _START_ to _END_ of _TOTAL_ users',
                        infoEmpty: 'No users available',
                        infoFiltered: '(filtered from _MAX_ total users)',
                        search: 'Search users:',
                        lengthMenu: 'Show _MENU_ users per page'
                    },
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                         '<"row"<"col-sm-12"tr>>' +
                         '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    initComplete: function() {
                        // Optimize search with debouncing
                        let searchTimer;
                        $('.dataTables_filter input').off('keyup search input').on('keyup search input', function() {
                            clearTimeout(searchTimer);
                            const searchTerm = this.value;
                            searchTimer = setTimeout(() => {
                                if (table && typeof table.search === 'function') {
                                    table.search(searchTerm).draw();
                                }
                            }, 500); // 500ms delay for better performance
                        });

                        // Style form controls
                        $('.dataTables_filter input').addClass('form-control form-control-sm');
                        $('.dataTables_length select').addClass('form-select form-select-sm');
                        
                        // Add loading indicator for AJAX requests
                        if (table) {
                            table.on('preXhr.dt', function() {
                                $('.dataTables_processing').show();
                            });
                            
                            table.on('xhr.dt', function() {
                                $('.dataTables_processing').hide();
                            });
                        }
                    },
                    drawCallback: function() {
                        // Re-initialize tooltips after each draw
                        try {
                            $('[title]').tooltip({
                                placement: 'top',
                                trigger: 'hover'
                            });
                        } catch (e) {
                            // Ignore tooltip errors
                        }
                    }
                });

                // Optimize column visibility for mobile
                if ($(window).width() < 768) {
                    table.columns([2, 4]).visible(false); // Hide username and roles on mobile
                }

                // Handle window resize
                $(window).on('resize', function() {
                    if (table) {
                        if ($(window).width() < 768) {
                            table.columns([2, 4]).visible(false);
                        } else {
                            table.columns([2, 4]).visible(true);
                        }
                        table.columns.adjust().draw();
                    }
                });

            } catch (error) {
                console.error('DataTable initialization failed:', error);
                // Enhanced fallback with retry option
                $('#users_datatable').html(`
                    <tbody>
                        <tr>
                            <td colspan="7" class="text-center p-5">
                                <div class="alert alert-warning border-0 shadow-sm">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Unable to load users data</strong><br>
                                    <small class="text-muted">Please check your connection and try refreshing the page.</small><br>
                                    <button class="btn btn-sm btn-outline-success mt-2" onclick="location.reload()">
                                        <i class="bi bi-arrow-clockwise me-1"></i>Retry
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                `);
            }
        });
    </script>
@endpush
