@extends('backend.layouts.app')

@section('title', 'Activity Logs Management')

@section('content')
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-success mb-1">
                        <i class="bi bi-activity me-2"></i>Activity Logs Management
                    </h2>
                    <p class="text-muted mb-0">Track and monitor system activities and user actions</p>
                </div>
                <div class="d-flex gap-2">
                    <!-- Login History feature disabled - route not implemented -->
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
                    <li class="breadcrumb-item active" aria-current="page">Activity Logs</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-table text-success me-2"></i>System Activity Logs
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Activity Logs DataTable -->
                    <div class="table-responsive">
                        <table class="table table-hover datatable" id="activity_datatable" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th width="10%">#</th>
                                    <th width="20%">Activity Type</th>
                                    <th width="45%">Description</th>
                                    <th width="25%">Timestamp</th>
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

    /* Activity type badges */
    .badge-log {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
        border-radius: 0.25rem;
    }

    .badge-created {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #155724;
    }

    .badge-updated {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #856404;
    }

    .badge-deleted {
        background-color: #ffebe9;
        color: #dc3545;
        border: 1px solid #dc3545;
    }

    /* Loading states */
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
            // Initialize DataTable with optimized settings for large datasets
            try {
                var table = $('#activity_datatable').DataTable({
                    processing: true,
                    serverSide: true, // Re-enabled for large datasets
                    responsive: true,
                    deferRender: true, // Only render visible rows
                    scroller: true, // Virtual scrolling for performance
                    scrollY: '60vh', // Fixed height with scrolling
                    ajax: {
                        url: "{{ route('backend.activity.index') }}",
                        type: 'GET',
                        error: function(xhr, error, code) {
                            console.log('Activity logs data could not be loaded:', error);
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
                            data: 'log_name',
                            name: 'log_name',
                            width: '20%',
                            render: function(data, type, row) {
                                if (type === 'display' && data) {
                                    var activityClasses = {
                                        'created': 'badge-created',
                                        'updated': 'badge-updated',
                                        'deleted': 'badge-deleted'
                                    };
                                    var activityClass = activityClasses[data.toLowerCase()] || 'badge-secondary';
                                    return '<span class="badge ' + activityClass + '">' + data + '</span>';
                                }
                                return data || '-';
                            }
                        },
                        {
                            data: 'description',
                            name: 'description',
                            width: '45%',
                            render: function(data, type, row) {
                                if (type === 'display' && data) {
                                    // Truncate long descriptions for performance
                                    const maxLength = 100;
                                    if (data.length > maxLength) {
                                        return '<div class="text-truncate" title="' + data + '">' + 
                                               data.substring(0, maxLength) + '...</div>';
                                    }
                                    return '<div title="' + data + '">' + data + '</div>';
                                }
                                return data || '-';
                            }
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            width: '25%',
                            render: function(data, type, row) {
                                if (type === 'display' && data) {
                                    try {
                                        var date = new Date(data);
                                        var now = new Date();
                                        var diffInSeconds = Math.floor((now - date) / 1000);

                                        if (diffInSeconds < 60) {
                                            return '<span class="text-success">Just now</span>';
                                        } else if (diffInSeconds < 3600) {
                                            var minutes = Math.floor(diffInSeconds / 60);
                                            return '<span class="text-info">' + minutes + ' minute' + (minutes > 1 ? 's' : '') + ' ago</span>';
                                        } else if (diffInSeconds < 86400) {
                                            var hours = Math.floor(diffInSeconds / 3600);
                                            return '<span class="text-warning">' + hours + ' hour' + (hours > 1 ? 's' : '') + ' ago</span>';
                                        } else {
                                            var days = Math.floor(diffInSeconds / 86400);
                                            return '<span class="text-muted">' + days + ' day' + (days > 1 ? 's' : '') + ' ago</span>';
                                        }
                                    } catch (e) {
                                        return data;
                                    }
                                }
                                return data || '-';
                            }
                        }
                    ],
                    order: [[3, 'desc']], // Default sort by timestamp (newest first)
                    language: {
                        processing: '<div class="d-flex align-items-center justify-content-center p-3"><div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>Loading activity logs...</div>',
                        emptyTable: 'No activity logs available',
                        zeroRecords: 'No matching activity logs found',
                        info: 'Showing _START_ to _END_ of _TOTAL_ activities',
                        infoEmpty: 'No activities available',
                        infoFiltered: '(filtered from _MAX_ total activities)',
                        search: 'Search activities:',
                        lengthMenu: 'Show _MENU_ activities per page'
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
                    table.columns([1]).visible(false); // Hide activity type on mobile
                }

                // Handle window resize
                $(window).on('resize', function() {
                    if ($(window).width() < 768) {
                        table.columns([1]).visible(false);
                    } else {
                        table.columns([1]).visible(true);
                    }
                    table.columns.adjust().draw();
                });

            } catch (error) {
                console.error('DataTable initialization failed:', error);
                // Enhanced fallback with retry option
                $('#activity_datatable').html(`
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center p-5">
                                <div class="alert alert-warning border-0 shadow-sm">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Unable to load activity logs</strong><br>
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
