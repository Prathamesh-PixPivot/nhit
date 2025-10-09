@extends('backend.layouts.app')

@section('title', 'Organizations')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center py-3">
                    <h5 class="mb-2 mb-md-0">
                        <i class="bi bi-building me-2"></i>Organizations Management
                    </h5>
                    <a href="{{ route('backend.organizations.create') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle me-1"></i>Add Organization
                    </a>
                </div>
                
                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3 mb-0" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show m-3 mb-0" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($organizations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4" style="min-width: 250px;">Organization</th>
                                        <th style="min-width: 100px;">Code</th>
                                        <th class="d-none d-lg-table-cell" style="min-width: 150px;">Database</th>
                                        <th style="min-width: 100px;">Status</th>
                                        <th class="d-none d-md-table-cell" style="min-width: 150px;">Created By</th>
                                        <th class="d-none d-md-table-cell" style="min-width: 120px;">Created</th>
                                        <th class="text-center pe-4" style="min-width: 180px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($organizations as $organization)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    @if($organization->logo)
                                                        <img src="{{ asset('storage/' . $organization->logo) }}" 
                                                             alt="{{ $organization->name }}" 
                                                             class="me-3 rounded" 
                                                             style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-primary bg-opacity-10 text-primary rounded d-flex align-items-center justify-content-center me-3" 
                                                             style="width: 40px; height: 40px; font-size: 14px; font-weight: 600;">
                                                            {{ strtoupper(substr($organization->code, 0, 2)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-semibold">{{ $organization->name }}</div>
                                                        @if($organization->description)
                                                            <small class="text-muted d-none d-sm-block">{{ Str::limit($organization->description, 50) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $organization->code }}</span>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <code class="small text-muted">{{ $organization->database_name }}</code>
                                            </td>
                                            <td>
                                                @if($organization->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="d-none d-md-table-cell">{{ $organization->creator->name ?? 'N/A' }}</td>
                                            <td class="d-none d-md-table-cell">{{ $organization->created_at->format('M d, Y') }}</td>
                                            <td class="text-center pe-4">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('backend.organizations.show', $organization) }}" 
                                                       class="btn btn-outline-info" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('backend.organizations.edit', $organization) }}" 
                                                       class="btn btn-outline-primary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="POST" 
                                                          action="{{ route('backend.organizations.toggle-status', $organization) }}" 
                                                          class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="btn btn-outline-{{ $organization->is_active ? 'warning' : 'success' }}" 
                                                                title="{{ $organization->is_active ? 'Deactivate' : 'Activate' }}"
                                                                onclick="return confirm('Are you sure you want to {{ $organization->is_active ? 'deactivate' : 'activate' }} this organization?')">
                                                            <i class="bi bi-{{ $organization->is_active ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" 
                                                          action="{{ route('backend.organizations.destroy', $organization) }}" 
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-outline-danger" 
                                                                title="Delete"
                                                                onclick="return confirm('Are you sure you want to delete this organization? This action cannot be undone!')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($organizations->hasPages())
                            <div class="d-flex justify-content-center p-3 border-top">
                                {{ $organizations->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-building display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">No Organizations Found</h4>
                            <p class="text-muted">Create your first organization to get started.</p>
                            <a href="{{ route('backend.organizations.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>Create Organization
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Organization Table Responsive Styles */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table {
    width: 100%;
    margin-bottom: 0;
}

.table td, .table th {
    white-space: nowrap;
    vertical-align: middle;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .btn-group-sm .btn {
        padding: 0.25rem 0.4rem;
        font-size: 0.75rem;
    }
    
    .table td, .table th {
        font-size: 0.85rem;
    }
}

/* Ensure full width */
.card {
    width: 100%;
}

.container-fluid {
    max-width: 100%;
}
</style>
@endsection
