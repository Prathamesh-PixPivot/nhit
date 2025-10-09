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
                    <div class="modern-search">
                        <i class="bi bi-search modern-search-icon"></i>
                        <input type="text" class="modern-input modern-search-input" placeholder="Search notes...">
                    </div>
                </div>
            </div>
            <div class="modern-card-body p-0">
                <!-- Modern DataTable -->
                <div class="table-responsive">
                    <table class="modern-table datatable" id="payment_notes_index_dt" style="width: 100%;">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="20%">Project</th>
                                <th width="20%">Vendor/Employee</th>
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
            var dt = $('#payment_notes_index_dt').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                deferRender: true,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                ajax: {
                    url: '{{ route('backend.payment-note.index') }}',
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
                    emptyTable: 'No payment notes found',
                    zeroRecords: 'No matching payment notes found'
                },
                initComplete: function() {
                    // Add search functionality styling
                    $('.dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_length select').addClass('form-select form-select-sm');
                }
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
                alert('Please select at least one note.');
                return;
            }

            document.getElementById('selectedNoteIds').value = selectedIds.join(',');
            document.getElementById('bulkBankLetterForm').submit();
        }
    </script>
@endpush
