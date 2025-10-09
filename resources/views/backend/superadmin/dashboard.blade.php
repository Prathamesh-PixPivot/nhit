@extends('backend.layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>SuperAdmin Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('backend.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">SuperAdmin</li>
            </ol>
        </nav>
    </div>

    <section class="section dashboard">
        <div class="row">
            <!-- Statistics Cards -->
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-xxl-2 col-md-4">
                        <div class="card info-card sales-card">
                            <div class="card-body">
                                <h5 class="card-title">Green Notes</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $stats['total_green_notes'] }}</h6>
                                        <span class="text-success small pt-1 fw-bold">Total</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-md-4">
                        <div class="card info-card revenue-card">
                            <div class="card-body">
                                <h5 class="card-title">Payment Notes</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-currency-dollar"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $stats['total_payment_notes'] }}</h6>
                                        <span class="text-success small pt-1 fw-bold">Total</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-md-4">
                        <div class="card info-card customers-card">
                            <div class="card-body">
                                <h5 class="card-title">Vendors</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $stats['total_vendors'] }}</h6>
                                        <span class="text-success small pt-1 fw-bold">Total</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-md-4">
                        <div class="card info-card">
                            <div class="card-body">
                                <h5 class="card-title">Users</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $stats['total_users'] }}</h6>
                                        <span class="text-success small pt-1 fw-bold">Total</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-md-4">
                        <div class="card info-card">
                            <div class="card-body">
                                <h5 class="card-title">Draft Notes</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-file-earmark-plus"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $stats['draft_payment_notes'] }}</h6>
                                        <span class="text-warning small pt-1 fw-bold">Drafts</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-2 col-md-4">
                        <div class="card info-card">
                            <div class="card-body">
                                <h5 class="card-title">On Hold</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-pause-circle"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $stats['held_green_notes'] }}</h6>
                                        <span class="text-danger small pt-1 fw-bold">Held</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Quick Actions</h5>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('backend.superadmin.payment-notes.create') }}" class="btn btn-primary w-100">
                                    <i class="bi bi-plus-circle"></i> Create Payment Note
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('backend.superadmin.green-notes.create') }}" class="btn btn-success w-100">
                                    <i class="bi bi-plus-circle"></i> Create Green Note
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('backend.superadmin.vendors.create') }}" class="btn btn-info w-100">
                                    <i class="bi bi-plus-circle"></i> Create Vendor
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('backend.superadmin.stats') }}" class="btn btn-warning w-100">
                                    <i class="bi bi-graph-up"></i> System Stats
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Management Links -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Management</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <i class="bi bi-file-earmark-text display-4 text-primary"></i>
                                        <h6 class="mt-2">Payment Notes</h6>
                                        <p class="text-muted small">Manage all payment notes</p>
                                        <a href="{{ route('backend.superadmin.payment-notes.index') }}" class="btn btn-primary btn-sm">
                                            Manage
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <i class="bi bi-file-earmark-check display-4 text-success"></i>
                                        <h6 class="mt-2">Green Notes</h6>
                                        <p class="text-muted small">Manage all green notes</p>
                                        <a href="{{ route('backend.superadmin.green-notes.index') }}" class="btn btn-success btn-sm">
                                            Manage
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-info">
                                    <div class="card-body text-center">
                                        <i class="bi bi-people display-4 text-info"></i>
                                        <h6 class="mt-2">Vendors</h6>
                                        <p class="text-muted small">Manage all vendors</p>
                                        <a href="{{ route('backend.superadmin.vendors.index') }}" class="btn btn-info btn-sm">
                                            Manage
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pending Approvals</h5>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            There are <strong>{{ $stats['pending_approvals'] }}</strong> green notes pending approval.
                        </div>
                        @if($stats['draft_payment_notes'] > 0)
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                There are <strong>{{ $stats['draft_payment_notes'] }}</strong> draft payment notes awaiting action.
                            </div>
                        @endif
                        @if($stats['held_green_notes'] > 0)
                            <div class="alert alert-danger">
                                <i class="bi bi-pause-circle"></i>
                                There are <strong>{{ $stats['held_green_notes'] }}</strong> green notes on hold.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
