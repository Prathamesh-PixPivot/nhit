@extends('backend.layouts.app')

@section('title', 'Bank Letters Management')

@section('content')
<div class="modern-container">
    <!-- Modern Header -->
    <div class="modern-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="modern-page-title">
                    <i class="bi bi-bank text-primary me-3"></i>Bank Letters
                </h1>
                <p class="modern-page-subtitle">Manage RTGS/NEFT bank letters and approval workflows</p>
            </div>
            <div class="d-flex gap-3">
                @can('create-bank-letter')
                    <a href="{{ route('backend.bank-letter.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Create Approval Rule
                    </a>
                @endcan
                <a href="{{ route('backend.bank-letter.dashboard') }}" class="btn btn-outline-info">
                    <i class="bi bi-speedometer2 me-1"></i>Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Modern Breadcrumb -->
    <div class="modern-breadcrumb">
        <a href="{{ route('backend.dashboard.index') }}">
            <i class="bi bi-house-door me-1"></i>Dashboard
        </a>
        <span class="modern-breadcrumb-separator">/</span>
        <span>Bank Letters</span>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card stats-card-primary">
                <div class="stats-icon">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['total'] ?? 0 }}</h3>
                    <p>Total Bank Letters</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card stats-card-warning">
                <div class="stats-icon">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['pending'] ?? 0 }}</h3>
                    <p>Pending Approval</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card stats-card-success">
                <div class="stats-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['approved'] ?? 0 }}</h3>
                    <p>Approved</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card stats-card-info">
                <div class="stats-icon">
                    <i class="bi bi-currency-rupee"></i>
                </div>
                <div class="stats-content">
                    <h3>₹{{ number_format($stats['total_amount'] ?? 0, 0) }}</h3>
                    <p>Total Approved Amount</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Approvals Section -->
    @if($pendingApprovals->isNotEmpty())
    <div class="modern-card mb-4">
        <div class="modern-card-header">
            <h3 class="mb-0">
                <i class="bi bi-exclamation-triangle text-warning me-2"></i>Pending Your Approval
            </h3>
        </div>
        <div class="modern-card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Bank Letter No.</th>
                            <th>Project</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingApprovals as $pending)
                        <tr>
                            <td><strong>{{ $pending['sl_no'] }}</strong></td>
                            <td>{{ $pending['project'] ?? 'N/A' }}</td>
                            <td><strong class="text-success">₹{{ number_format($pending['total_amount'], 2) }}</strong></td>
                            <td>{{ $pending['created_at']->format('d/m/Y h:i A') }}</td>
                            <td>
                                <a href="{{ route('backend.bank-letter.approve-form', $pending['sl_no']) }}" 
                                   class="btn btn-primary btn-sm">
                                    <i class="bi bi-check-circle me-1"></i>Review & Approve
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Modern Status Tabs -->
    <div class="modern-tabs">
        <a href="{{ route('backend.bank-letter.index', ['status' => 'all']) }}" 
           class="modern-tab {{ request('status') === 'all' || !request('status') ? 'active' : '' }}">
            <i class="bi bi-list-ul me-2"></i>All Letters
        </a>
        <a href="{{ route('backend.bank-letter.index', ['status' => 'S']) }}" 
           class="modern-tab {{ request('status') === 'S' ? 'active' : '' }}">
            <i class="bi bi-clock me-2"></i>Pending
        </a>
        <a href="{{ route('backend.bank-letter.index', ['status' => 'A']) }}" 
           class="modern-tab {{ request('status') === 'A' ? 'active' : '' }}">
            <i class="bi bi-check-circle me-2"></i>Approved
        </a>
        <a href="{{ route('backend.bank-letter.index', ['status' => 'R']) }}" 
           class="modern-tab {{ request('status') === 'R' ? 'active' : '' }}">
            <i class="bi bi-x-circle me-2"></i>Rejected
        </a>
    </div>

    <div class="modern-content">
        <!-- Modern Data Table Card -->
        <div class="modern-card">
            <div class="modern-card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1 text-gray-900">
                            <i class="bi bi-table text-primary me-2"></i>Bank Letters
                        </h3>
                        @if(request('status'))
                            <span class="modern-badge modern-badge-info">
                                {{ ucfirst(request('status')) }} Status
                            </span>
                        @endif
                    </div>
                    <div class="modern-search">
                        <i class="bi bi-search modern-search-icon"></i>
                        <input type="text" class="modern-input modern-search-input" placeholder="Search bank letters...">
                    </div>
                </div>
            </div>
            <div class="modern-card-body p-0">
                <!-- Modern DataTable -->
                <div class="table-responsive">
                    <table class="modern-table" id="bank_letters_dt" style="width: 100%;">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="15%">Bank Letter No.</th>
                                <th width="20%">Project</th>
                                <th width="15%">Amount</th>
                                <th width="15%">Date</th>
                                <th width="15%">Status</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Rules Section -->
    <div class="modern-card mt-4">
        <div class="modern-card-header">
            <h3 class="mb-0">
                <i class="bi bi-gear text-primary me-2"></i>Approval Rules Configuration
            </h3>
        </div>
        <div class="modern-card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Approver Level</th>
                            <th>Users</th>
                            <th>Payment Range</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($approvalSteps as $rule)
                            @foreach ($rule->approvers->groupBy('approver_level') as $level => $approvers)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">Approver {{ $level }}</span>
                                        @if ($rule->min_amount == 0 && $rule->max_amount == 0)
                                            <small class="text-muted d-block">(Internal)</small>
                                        @else
                                            <small class="text-muted d-block">(External)</small>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($approvers as $approver)
                                            <span class="badge bg-light text-dark me-1">{{ $approver->user->name ?? 'N/A' }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if ($rule->min_amount == 0 && $rule->max_amount == 0)
                                            <span class="badge bg-success">NO LIMIT</span>
                                        @else
                                            @if ($rule->max_amount == null)
                                                <strong>₹{{ number_format($rule->min_amount) }}</strong> And Above
                                            @else
                                                <strong>₹{{ number_format($rule->min_amount) }}</strong> - 
                                                <strong>₹{{ number_format($rule->max_amount) }}</strong>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @can(['bank-letter-edit-rule'])
                                            <a href="{{ route('backend.bank-letter.edit', $rule->id) }}" 
                                               class="btn btn-outline-primary btn-sm" title="Edit Rule">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @endcan
                                        <a href="{{ route('backend.bank-letter.show', $rule->id) }}" 
                                           class="btn btn-outline-info btn-sm" title="View Rule">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @can(['bank-letter-delete-rule'])
                                            <form action="{{ route('backend.bank-letter.destroy', $rule->id) }}" 
                                                  method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                        onclick="return confirm('Are you sure you want to delete this approval rule?')" 
                                                        title="Delete Rule">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Modern Container Styles */
    .modern-container {
        padding: 1.5rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 100vh;
    }

    .modern-header {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .modern-page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .modern-page-subtitle {
        color: #6c757d;
        font-size: 1.1rem;
        margin-bottom: 0;
    }

    .modern-breadcrumb {
        background: white;
        border-radius: 0.75rem;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        font-size: 0.95rem;
    }

    .modern-breadcrumb a {
        color: #007bff;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .modern-breadcrumb a:hover {
        color: #0056b3;
    }

    .modern-breadcrumb-separator {
        margin: 0 0.75rem;
        color: #6c757d;
    }

    /* Statistics Cards */
    .stats-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 1rem;
    }

    .stats-card-primary .stats-icon {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
    }

    .stats-card-warning .stats-icon {
        background: linear-gradient(135deg, #ffc107, #e0a800);
        color: white;
    }

    .stats-card-success .stats-icon {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: white;
    }

    .stats-card-info .stats-icon {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
    }

    .stats-content h3 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        color: #2c3e50;
    }

    .stats-content p {
        color: #6c757d;
        margin-bottom: 0;
        font-size: 0.9rem;
    }

    .modern-tabs {
        background: white;
        border-radius: 1rem;
        padding: 0.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        display: flex;
        gap: 0.25rem;
        flex-wrap: wrap;
    }

    .modern-tab {
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        text-decoration: none;
        color: #6c757d;
        font-weight: 500;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        white-space: nowrap;
    }

    .modern-tab:hover {
        background: #f8f9fa;
        color: #007bff;
        transform: translateY(-1px);
    }

    .modern-tab.active {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        box-shadow: 0 4px 15px rgba(0,123,255,0.3);
    }

    .modern-content {
        margin-bottom: 2rem;
    }

    .modern-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .modern-card-header {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        padding: 1.5rem 2rem;
        border-bottom: 1px solid rgba(0,0,0,0.08);
    }

    .modern-card-body {
        padding: 1.5rem 2rem;
    }

    .modern-search {
        position: relative;
        width: 300px;
    }

    .modern-search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 2;
    }

    .modern-search-input {
        padding-left: 2.5rem !important;
        border-radius: 2rem !important;
        border: 2px solid #e9ecef !important;
        transition: all 0.3s ease !important;
    }

    .modern-search-input:focus {
        border-color: #007bff !important;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25) !important;
    }

    .modern-input {
        border: 2px solid #e9ecef;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .modern-badge {
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .modern-badge-info {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
    }

    .modern-table {
        margin-bottom: 0 !important;
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        color: #495057;
        padding: 1rem 0.75rem;
    }

    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
        color: #212529;
        padding: 1rem 0.75rem;
    }

    .table-responsive {
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0,123,255,0.05);
    }
</style>
@endpush

@push('script')
<script>
    $(function () {
        // Initialize DataTable
        var dt = $('#bank_letters_dt').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            ajax: {
                url: '{{ route('backend.bank-letter.index') }}',
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
                    data: 'sl_no',
                    name: 'sl_no',
                    width: '15%',
                    render: function(data, type, row) {
                        return '<strong class="text-primary">' + data + '</strong>';
                    }
                },
                {
                    data: 'project',
                    name: 'project',
                    width: '20%',
                    render: function(data, type, row) {
                        return data || '<span class="text-muted">N/A</span>';
                    }
                },
                {
                    data: 'formatted_amount',
                    name: 'total_amount',
                    width: '15%',
                    orderable: true
                },
                {
                    data: 'formatted_date',
                    name: 'created_at',
                    width: '15%'
                },
                {
                    data: 'status_badge',
                    name: 'status',
                    width: '15%',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    width: '15%'
                }
            ],
            language: {
                processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div> Processing...',
                emptyTable: 'No bank letters found',
                zeroRecords: 'No matching bank letters found'
            },
            initComplete: function() {
                // Add search functionality styling
                $('.dataTables_filter input').addClass('form-control form-control-sm');
                $('.dataTables_length select').addClass('form-select form-select-sm');
            }
        });
        
        // Custom search functionality
        $('.modern-search-input').on('keyup', function() {
            dt.search(this.value).draw();
        });
    });
</script>
@endpush
