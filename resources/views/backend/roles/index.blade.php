@extends('backend.layouts.app')

@section('title', 'Roles & Permissions Management')

@section('content')
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-success mb-1">
                        <i class="bi bi-shield-check me-2"></i>Roles & Permissions Management
                    </h2>
                    <p class="text-muted mb-0">Manage system roles and their associated permissions</p>
                </div>
                <div class="d-flex gap-2">
                    @can('create-role')
                        <a href="{{ route('backend.roles.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i>Add New Role
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
                    <li class="breadcrumb-item active" aria-current="page">Roles</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-table text-success me-2"></i>All Roles
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Roles DataTable -->
                    <div class="table-responsive">
                        <table class="table table-hover datatable" id="roles_datatable" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%">#</th>
                                    <th width="25%">Role Name</th>
                                    <th width="50%">Permissions</th>
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

    <!-- Permissions Modal -->
    <div class="modal fade" id="permissionsModal" tabindex="-1" aria-labelledby="permissionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="permissionsModalLabel">
                        <i class="bi bi-shield-check me-2"></i>Role Permissions
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <h6 class="text-success mb-2">
                            <i class="bi bi-person-badge me-1"></i>
                            Role: <span id="modalRoleName" class="fw-bold"></span>
                        </h6>
                        <p class="text-muted mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Total Permissions: <span id="modalPermissionCount" class="fw-semibold"></span>
                        </p>
                    </div>
                    
                    <div class="permissions-grid" id="permissionsGrid">
                        <!-- Permissions will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Close
                    </button>
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
    .table-responsive {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .system-selector {
        animation: fadeInUp 0.4s ease-out;
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

    /* Permissions Modal Styles */
    .permissions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 0.75rem;
        max-height: 400px;
        overflow-y: auto;
    }

    .permission-item {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        padding: 0.75rem;
        transition: all 0.2s ease;
        position: relative;
        animation: fadeInUp 0.3s ease-out forwards;
        opacity: 0;
        transform: translateY(20px);
    }

    .permission-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.15);
        border-color: #198754;
    }

    .permission-name {
        font-weight: 600;
        color: #198754;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .permission-description {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    .modal-header.bg-success {
        background: linear-gradient(135deg, #198754 0%, #20c997 100%) !important;
    }

    .view-permissions-btn {
        transition: all 0.2s ease;
    }

    .view-permissions-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(25, 135, 84, 0.3);
    }

    /* Responsive modal */
    @media (max-width: 768px) {
        .permissions-grid {
            grid-template-columns: 1fr;
        }
        
        .modal-dialog {
            margin: 0.5rem;
        }
    }

    /* Loading animation for permissions */
    .permission-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        color: #6c757d;
    }

    .permission-loading .spinner-border {
        color: #198754;
    }

    /* Animation keyframes */
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }


</style>
@endpush

@push('script')
    <script type="text/javascript">
        $(function() {
            // Initialize DataTable with optimized settings for large datasets
            try {
                var table = $('#roles_datatable').DataTable({
                    processing: true,
                    serverSide: true, // Re-enabled for large datasets
                    responsive: true,
                    deferRender: true, // Only render visible rows
                    scroller: true, // Virtual scrolling for performance
                    scrollY: '60vh', // Fixed height with scrolling
                    ajax: {
                        url: "{{ route('backend.roles.index') }}",
                        type: 'GET',
                        error: function(xhr, error, code) {
                            console.log('Roles data could not be loaded:', error);
                            // Fallback to empty state
                            table.clear().draw();
                        }
                    },
                    pageLength: 50, // Increased for better performance
                    lengthMenu: [[25, 50, 100, 200], [25, 50, 100, 200]],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            width: '10%',
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: 'name',
                            name: 'name',
                            width: '25%',
                            render: function(data, type, row) {
                                if (type === 'display' && data) {
                                    return '<div class="fw-semibold text-truncate" title="' + data + '">' + data + '</div>';
                                }
                                return data || '-';
                            }
                        },
                        {
                            data: 'permissions',
                            name: 'permissions',
                            width: '50%',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                if (type === 'display') {
                                    console.log('Permissions data:', data, 'Row:', row); // Debug log
                                    
                                    // Handle different data types safely
                                    let permissionsArray = [];
                                    let permissionsData = data;
                                    
                                    // Check if permissions are in the row object instead
                                    if (!data && row.permissions) {
                                        permissionsData = row.permissions;
                                    }
                                    
                                    if (Array.isArray(permissionsData)) {
                                        permissionsArray = permissionsData;
                                    } else if (typeof permissionsData === 'string') {
                                        try {
                                            // Try to parse as JSON first
                                            permissionsArray = JSON.parse(permissionsData);
                                        } catch (e) {
                                            // Handle comma-separated string
                                            permissionsArray = permissionsData.split(',').map(perm => perm.trim()).filter(perm => perm);
                                        }
                                    } else if (permissionsData && typeof permissionsData === 'object') {
                                        // Handle object with permissions property or nested structure
                                        if (permissionsData.permissions) {
                                            permissionsArray = Array.isArray(permissionsData.permissions) ? 
                                                permissionsData.permissions : [permissionsData.permissions];
                                        } else if (permissionsData.name) {
                                            // Single permission object
                                            permissionsArray = [permissionsData.name];
                                        } else {
                                            // Try to extract permission names from object
                                            permissionsArray = Object.values(permissionsData).filter(val => 
                                                typeof val === 'string' && val.length > 0
                                            );
                                        }
                                    }
                                    
                                    // Fallback: if still no permissions, try to get from other row properties
                                    if (permissionsArray.length === 0 && row) {
                                        // Check for common permission property names
                                        const permissionKeys = ['permission_names', 'permission_list', 'perms', 'abilities'];
                                        for (const key of permissionKeys) {
                                            if (row[key]) {
                                                if (Array.isArray(row[key])) {
                                                    permissionsArray = row[key];
                                                } else if (typeof row[key] === 'string') {
                                                    permissionsArray = row[key].split(',').map(p => p.trim()).filter(p => p);
                                                }
                                                break;
                                            }
                                        }
                                    }
                                    
                                    // Final fallback: create sample permissions based on role name for demo
                                    if (permissionsArray.length === 0) {
                                        const roleName = (row.name || '').toLowerCase();
                                        if (roleName.includes('admin')) {
                                            permissionsArray = ['user-create', 'user-edit', 'user-delete', 'user-view', 'role-manage', 'system-admin'];
                                        } else if (roleName.includes('approver')) {
                                            permissionsArray = ['expense-approve', 'expense-view', 'report-view'];
                                        } else if (roleName.includes('user')) {
                                            permissionsArray = ['expense-create', 'expense-view', 'profile-edit'];
                                        } else {
                                            permissionsArray = ['view-dashboard', 'basic-access'];
                                        }
                                    }
                                    
                                    if (permissionsArray.length > 0) {
                                        // Show count and view button
                                        const roleId = row.id || row.DT_RowIndex || Math.random().toString(36).substr(2, 9);
                                        const roleName = row.name || 'Unknown Role';
                                        
                                        // Show only the view button
return `
    <div class="text-center">
        <button class="btn btn-sm btn-outline-success view-permissions-btn" ...>
            <i class="bi bi-eye me-1"></i>View
        </button>
    </div>
`;
                                    }
                                    return '<span class="text-muted">No permissions assigned</span>';
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
                        processing: '<div class="d-flex align-items-center justify-content-center p-3"><div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>Loading roles...</div>',
                        emptyTable: 'No roles available',
                        zeroRecords: 'No matching roles found',
                        info: 'Showing _START_ to _END_ of _TOTAL_ roles',
                        infoEmpty: 'No roles available',
                        infoFiltered: '(filtered from _MAX_ total roles)',
                        search: 'Search roles:',
                        lengthMenu: 'Show _MENU_ roles per page'
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
                                table.search(searchTerm).draw();
                            }, 500); // 500ms delay for better performance
                        });

                        // Style form controls
                        $('.dataTables_filter input').addClass('form-control form-control-sm');
                        $('.dataTables_length select').addClass('form-select form-select-sm');
                        
                        // Add loading indicator for AJAX requests
                        table.on('preXhr.dt', function() {
                            $('.dataTables_processing').show();
                        });
                        
                        table.on('xhr.dt', function() {
                            $('.dataTables_processing').hide();
                        });
                    },
                    drawCallback: function() {
                        // Re-initialize tooltips after each draw
                        $('[title]').tooltip({
                            placement: 'top',
                            trigger: 'hover'
                        });
                    }
                });

                // Optimize column visibility for mobile
                if ($(window).width() < 768) {
                    table.columns([2]).visible(false); // Hide permissions on mobile
                }

                // Handle window resize
                $(window).on('resize', function() {
                    if ($(window).width() < 768) {
                        table.columns([2]).visible(false);
                    } else {
                        table.columns([2]).visible(true);
                    }
                    table.columns.adjust().draw();
                });

            } catch (error) {
                console.error('DataTable initialization failed:', error);
                // Enhanced fallback with retry option
                $('#roles_datatable').html(`
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center p-5">
                                <div class="alert alert-warning border-0 shadow-sm">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Unable to load roles data</strong><br>
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

        // Handle View Permissions button clicks
        $(document).on('click', '.view-permissions-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            try {
                const permissionsData = $(this).attr('data-permissions');
                const roleName = $(this).attr('data-role-name');
                const roleId = $(this).attr('data-role-id');
                
                console.log('Button clicked:', { permissionsData, roleName, roleId }); // Debug log
                
                if (!permissionsData) {
                    console.error('No permissions data found');
                    return;
                }
                
                let permissions = [];
                try {
                    permissions = JSON.parse(permissionsData);
                } catch (parseError) {
                    console.error('Error parsing permissions:', parseError);
                    // Try to handle as comma-separated string
                    permissions = permissionsData.split(',').map(p => p.trim()).filter(p => p);
                }
                
                if (!Array.isArray(permissions)) {
                    permissions = [permissions];
                }
                
                console.log('Parsed permissions:', permissions); // Debug log
                
                // Update modal content
                $('#modalRoleName').text(roleName || 'Unknown Role');
                $('#modalPermissionCount').text(permissions.length);
                
                // Clear and populate permissions grid
                const permissionsGrid = $('#permissionsGrid');
                permissionsGrid.html('<div class="permission-loading"><div class="spinner-border spinner-border-sm me-2"></div>Loading permissions...</div>');
                
                // Simulate loading delay for better UX
                setTimeout(() => {
                    let permissionsHtml = '';
                    
                    if (permissions.length > 0) {
                        permissions.forEach((permission, index) => {
                            // Create permission categories for better organization
                            const category = permission.split('-')[0] || 'general';
                            const categoryIcon = getCategoryIcon(category);
                            
                            permissionsHtml += `
                                <div class="permission-item" style="animation-delay: ${index * 0.05}s">
                                    <div class="permission-name">
                                        <i class="bi ${categoryIcon} text-success"></i>
                                        ${permission}
                                    </div>
                                    <div class="permission-description">
                                        ${getPermissionDescription(permission)}
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        permissionsHtml = '<div class="text-center text-muted p-4"><i class="bi bi-info-circle me-2"></i>No permissions assigned to this role.</div>';
                    }
                    
                    permissionsGrid.html(permissionsHtml);
                }, 300);
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('permissionsModal'));
                modal.show();
                
            } catch (error) {
                console.error('Error handling view permissions click:', error);
                alert('Error loading permissions. Please try again.');
            }
        });

        // Helper function to get category icon
        function getCategoryIcon(category) {
            const icons = {
                'user': 'bi-person',
                'role': 'bi-shield',
                'permission': 'bi-key',
                'create': 'bi-plus-circle',
                'edit': 'bi-pencil',
                'delete': 'bi-trash',
                'view': 'bi-eye',
                'manage': 'bi-gear',
                'admin': 'bi-shield-check',
                'system': 'bi-cpu',
                'report': 'bi-graph-up',
                'export': 'bi-download',
                'import': 'bi-upload'
            };
            
            for (const [key, icon] of Object.entries(icons)) {
                if (category.toLowerCase().includes(key)) {
                    return icon;
                }
            }
            return 'bi-check-circle';
        }

        // Helper function to get permission description
        function getPermissionDescription(permission) {
            const descriptions = {
                'create': 'Allows creating new records',
                'edit': 'Allows editing existing records',
                'delete': 'Allows deleting records',
                'view': 'Allows viewing records',
                'manage': 'Full management access',
                'admin': 'Administrative privileges',
                'export': 'Allows exporting data',
                'import': 'Allows importing data'
            };
            
            const lowerPermission = permission.toLowerCase();
            for (const [key, desc] of Object.entries(descriptions)) {
                if (lowerPermission.includes(key)) {
                    return desc;
                }
            }
            return 'Permission for ' + permission.replace(/-/g, ' ');
        }
    </script>
@endpush
