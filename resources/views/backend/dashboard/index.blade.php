@extends('backend.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <section class="section dashboard">
        <!-- Welcome Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-primary mb-1">
                            <i class="bi bi-speedometer2 me-2"></i>Welcome back, {{ Auth::user()->name }}!
                        </h2>
                        <p class="text-muted mb-0">Here's what's happening with your notes today</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-calendar me-1"></i>{{ date('M Y') }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Current Month</a></li>
                                <li><a class="dropdown-item" href="#">Last Month</a></li>
                                <li><a class="dropdown-item" href="#">Last 3 Months</a></li>
                            </ul>
                        </div>
                        <button class="btn btn-primary">
                            <i class="bi bi-download me-1"></i>Export Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Cards Row -->
        <div class="row mb-4">
            <!-- Total Notes KPI -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                                <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 fs-6">
                                +{{ collect($dataTill ?? [])->sum('value') > 0 ? number_format(min((collect($dataCurrent ?? [])->sum('value') / collect($dataTill ?? [])->sum('value')) * 100, 999), 1) : 0 }}%
                            </span>
                        </div>
                        <h3 class="fw-bold text-primary mb-2">
                            {{ number_format(max(collect($dataTill ?? [])->sum('value'), 0), 0) }}</h3>
                        <p class="text-muted mb-1">Total Notes</p>
                        <small class="text-success">
                            <i class="bi bi-arrow-up"></i>
                            +{{ collect($dataTill ?? [])->sum('value') > 0 ? number_format(min((collect($dataCurrent ?? [])->sum('value') / collect($dataTill ?? [])->sum('value')) * 100, 999), 1) : 0 }}%
                            from last month
                        </small>
                    </div>
                </div>
            </div>

            <!-- Pending Approvals KPI -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                                <i class="bi bi-clock text-warning fs-4"></i>
                            </div>
                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 fs-6">
                                {{ number_format(max(collect($userData ?? [])->where('payment_statuses', '!=', '-')->count() +collect($userData ?? [])->where('green_statuses', '!=', '-')->count() +collect($userData ?? [])->where('reimbursement_statuses', '!=', '-')->count(),0),0) }}
                            </span>
                        </div>
                        <h3 class="fw-bold text-warning mb-2">
                            {{ number_format(max(collect($userData ?? [])->where('payment_statuses', '!=', '-')->count() +collect($userData ?? [])->where('green_statuses', '!=', '-')->count() +collect($userData ?? [])->where('reimbursement_statuses', '!=', '-')->count(),0),0) }}
                        </h3>
                        <p class="text-muted mb-1">Pending Approvals</p>
                        <small class="text-muted">Require your attention</small>
                    </div>
                </div>
            </div>

            <!-- Completed This Month KPI -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-success bg-opacity-10 p-3 rounded-3">
                                <i class="bi bi-check-circle text-success fs-4"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 fs-6">
                                +{{ collect($dataTill ?? [])->where('name', 'Approved')->sum('value') > 0? number_format(min((collect($dataCurrent ?? [])->where('name', 'Approved')->sum('value') /collect($dataTill ?? [])->where('name', 'Approved')->sum('value')) *100,999),1): 0 }}%
                            </span>
                        </div>
                        <h3 class="fw-bold text-success mb-2">
                            {{ number_format(max(collect($dataCurrent ?? [])->where('name', 'Approved')->sum('value') +collect($dataCurrent ?? [])->where('name', 'Paid')->sum('value'),0),0) }}
                        </h3>
                        <p class="text-muted mb-1">Completed This Month</p>
                        <small class="text-success">
                            <i class="bi bi-arrow-up"></i>
                            +{{ collect($dataTill ?? [])->where('name', 'Approved')->sum('value') > 0? number_format(min((collect($dataCurrent ?? [])->where('name', 'Approved')->sum('value') /collect($dataTill ?? [])->where('name', 'Approved')->sum('value')) *100,999),1): 0 }}%
                            vs last month
                        </small>
                    </div>
                </div>
            </div>

            <!-- Active Users KPI -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-info bg-opacity-10 p-3 rounded-3">
                                <i class="bi bi-people text-info fs-4"></i>
                            </div>
                            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 fs-6">+3</span>
                        </div>
                        <h3 class="fw-bold text-info mb-2">{{ number_format(max(count($userData ?? []), 0), 0) }}</h3>
                        <p class="text-muted mb-1">Active Users</p>
                        <small class="text-info">
                            <i class="bi bi-arrow-up"></i> +3 new this week
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent Activity -->
        <div class="row mb-4">
            <!-- Quick Actions -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-lightning-charge text-warning me-2"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            @can(['create-note'])
                                <div class="col-6">
                                    <a href="{{ route('backend.note.create') }}"
                                        class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2 py-3 hover-effect">
                                        <i class="bi bi-plus-circle"></i>
                                        <span>New Note</span>
                                    </a>
                                </div>
                            @endcan
                            @can(['create-reimbursement-note'])
                                <div class="col-6">
                                    <a href="{{ route('backend.reimbursement-note.create') }}"
                                        class="btn btn-outline-success w-100 d-flex align-items-center justify-content-center gap-2 py-3 hover-effect">
                                        <i class="bi bi-receipt"></i>
                                        <span>Reimbursement</span>
                                    </a>
                                </div>
                            @endcan
                            @can(['create-payment'])
                                <div class="col-6">
                                    <a href="{{ route('backend.payments.create') }}"
                                        class="btn btn-outline-info w-100 d-flex align-items-center justify-content-center gap-2 py-3 hover-effect">
                                        <i class="bi bi-credit-card"></i>
                                        <span>Payment</span>
                                    </a>
                                </div>
                            @endcan
                            <div class="col-6">
                                <a href="{{ route('backend.activity.index') }}"
                                    class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2 py-3 hover-effect">
                                    <i class="bi bi-activity"></i>
                                    <span>View Activity</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clock-history text-info me-2"></i>Recent Activity
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="timeline">
                            @php
                                // Get recent login history (last 8 records)
                                $recentLogins = \App\Models\UserLoginHistory::with('user')
                                    ->orderBy('created_at', 'desc')
                                    ->limit(8)
                                    ->get();
                            @endphp

                            @if ($recentLogins->count() > 0)
                                @foreach ($recentLogins as $activity)
                                    @if ($activity->user)
                                        <div class="timeline-item mb-3">
                                            <div class="timeline-marker bg-primary"></div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1">{{ $activity->user->name }} logged in</h6>
                                                <small class="text-muted">{{ $activity->user->name }} •
                                                    {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-clock-history fs-1 mb-3"></i>
                                    <p>No recent activity found</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="row">
            <!-- All Notes Table -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-table text-primary me-2"></i>All Notes Overview
                        </h5>
                        @can(['export-excel-note'])
                            <form method="GET" action="{{ route('backend.note.export.note.excel') }}"
                                class="d-flex gap-2">
                                <input type="date" class="form-control" name="start_date"
                                    value="{{ request('start_date') }}" placeholder="Start Date">
                                <input type="date" class="form-control" name="end_date"
                                    value="{{ request('end_date') }}" placeholder="End Date">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-file-earmark-arrow-down-fill me-1"></i>Export Excel
                                </button>
                            </form>
                        @endcan
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Green Note</th>
                                        <th>Payment Note</th>
                                        <th>Reimbursement Note</th>
                                        <th>Bank Letter Note</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($userData ?? [] as $index => $item)
                                        <tr class="hover-row">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                                        style="width: 32px; height: 32px;">
                                                        {{ strtoupper(substr($item['name'], 0, 1)) }}
                                                    </div>
                                                    <strong>{{ $item['name'] }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-success bg-opacity-10 text-success">
                                                    {{ $item['green_statuses'] ?: '-' }}
                                                </span>
                                                @if ($item['id'] == auth()->id() && !empty($item['green_ids']))
                                                    <form action="{{ route('backend.dashboard.user.green.notes') }}"
                                                        method="POST" id="greenNoteForm-{{ $item['id'] }}"
                                                        class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="ids"
                                                            value="{{ implode(',', $item['green_ids']) }}">
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-primary ms-1">
                                                            <i class="bi bi-arrow-right"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info bg-opacity-10 text-info">
                                                    {{ $item['payment_statuses'] ?: '-' }}
                                                </span>
                                                @if ($item['id'] == auth()->id() && !empty($item['payment_ids']))
                                                    <form action="{{ route('backend.dashboard.user.payment.notes') }}"
                                                        method="POST" id="paymentNoteForm-{{ $item['id'] }}"
                                                        class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="ids"
                                                            value="{{ implode(',', $item['payment_ids']) }}">
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-primary ms-1">
                                                            <i class="bi bi-arrow-right"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-warning bg-opacity-10 text-warning">
                                                    {{ $item['reimbursement_statuses'] ?: '-' }}
                                                </span>
                                                @if ($item['id'] == auth()->id() && !empty($item['reimbursement_ids']))
                                                    <form
                                                        action="{{ route('backend.dashboard.user.reimbursement.notes') }}"
                                                        method="POST" id="reimbursementNoteForm-{{ $item['id'] }}"
                                                        class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="ids"
                                                            value="{{ implode(',', $item['reimbursement_ids']) }}">
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-primary ms-1">
                                                            <i class="bi bi-arrow-right"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                    {{ $item['bankLetter_statuses'] ?: '-' }}
                                                </span>
                                                @if ($item['id'] == auth()->id() && !empty($item['bankLetter_ids']))
                                                    <form action="{{ route('backend.dashboard.user.bank.letter.notes') }}"
                                                        method="POST" id="bankLetterNoteForm-{{ $item['id'] }}"
                                                        class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="ids"
                                                            value="{{ implode(',', $item['bankLetter_ids']) }}">
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-primary ms-1">
                                                            <i class="bi bi-arrow-right"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('backend.dashboard.filter', ['id' => $item['id']]) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Charts Section (only for admin users) -->
        @if (auth()->user()->hasRole('Super Admin Live') || auth()->user()->can('show-dashboard'))
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-bar-chart text-primary me-2"></i>Analytics Overview
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-center mb-3">Expense Approval Notes - Current Month</h6>
                                    <div id="trafficCharta" style="min-height: 300px;" class="echart"></div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-center mb-3">Expense Approval Notes - Till Date</h6>
                                    <div id="trafficChartb" style="min-height: 300px;" class="echart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </section>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #f8f9fa;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1rem;
        }

        .timeline-marker {
            position: absolute;
            left: -23px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 3px solid #ffffff;
            box-shadow: 0 0 0 2px #e9ecef;
        }

        .timeline-content h6 {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .timeline-content small {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: #ffffff;
            border: 1px solid #e9ecef;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .hover-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15) !important;
        }

        .hover-effect {
            transition: all 0.3s ease;
            border-radius: 0.5rem;
        }

        .hover-effect:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .hover-row {
            transition: background-color 0.2s ease;
        }

        .hover-row:hover {
            background-color: #f8f9fa;
        }

        /* Enhanced badge styling */
        .badge {
            font-weight: 500;
            letter-spacing: 0.025em;
        }

        /* Better spacing for card headers */
        .card-header {
            background-color: #fafbfc;
            border-bottom: 1px solid #e9ecef;
        }

        /* Improved table styling */
        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Custom scrollbar for table */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Ensure all backgrounds are clean */
        .bg-primary {
            background-color: #007bff !important;
        }

        .bg-success {
            background-color: #28a745 !important;
        }

        .bg-info {
            background-color: #17a2b8 !important;
        }

        .bg-warning {
            background-color: #ffc107 !important;
        }

        .bg-danger {
            background-color: #dc3545 !important;
        }

        /* Light theme text colors */
        .text-muted {
            color: #6c757d !important;
        }

        .text-primary {
            color: #007bff !important;
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-info {
            color: #17a2b8 !important;
        }

        .text-warning {
            color: #ffc107 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        /* Enhanced button styling */
        .btn {
            font-weight: 500;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Icon styling improvements */
        .card-title i {
            opacity: 0.8;
        }

        /* Better responsive design */
        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem 1rem;
            }

            .btn {
                font-size: 0.875rem;
            }

            .card-title {
                font-size: 1.1rem;
            }
        }

        /* Loading animation for cards */
        @keyframes shimmer {
            0% {
                background-position: -200px 0;
            }

            100% {
                background-position: calc(200px + 100%) 0;
            }
        }

        .loading-card {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200px 100%;
            animation: shimmer 1.5s infinite;
        }
    </style>

    {{-- Load dashboard charts script --}}
    <script src="{{ asset('theme/assets/js/dashboard-charts.js') }}"></script>

    {{-- Initialize charts with data --}}
    @if (isset($dataCurrent))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var chartData = {!! json_encode($dataCurrent) !!};
                initChartA(chartData);
            });
        </script>
    @endif

    @if (isset($dataTill))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var chartDataB = {!! json_encode($dataTill) !!};
                initChartB(chartDataB);
            });
        </script>
    @endif
@endsection
