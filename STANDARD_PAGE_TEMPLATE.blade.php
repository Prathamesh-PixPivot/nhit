{{-- STANDARD PAGE TEMPLATE - Use this structure for ALL pages --}}
@extends('backend.layouts.app')

@section('title', 'Page Title')

@section('content')
<div class="modern-container">
    {{-- Modern Header - CONSISTENT across all pages --}}
    <div class="modern-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="modern-page-title">
                    <i class="bi bi-icon text-primary me-3"></i>Page Title
                </h1>
                <p class="modern-page-subtitle">Page description</p>
            </div>
            <div class="d-flex gap-3">
                {{-- Use standard Bootstrap buttons, not custom ones --}}
                <a href="#" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Primary Action
                </a>
            </div>
        </div>
    </div>

    {{-- Modern Breadcrumb - CONSISTENT across all pages --}}
    <div class="modern-breadcrumb">
        <a href="{{ route('backend.dashboard.index') }}">
            <i class="bi bi-house-door me-1"></i>Dashboard
        </a>
        <span class="modern-breadcrumb-separator">/</span>
        <span>Current Page</span>
    </div>

    {{-- Modern Content Area - CONSISTENT spacing --}}
    <div class="modern-content">
        
        {{-- FOR INDEX PAGES: Use this structure --}}
        {{-- Modern Tabs (if needed) --}}
        <div class="modern-tabs">
            <a href="#" class="modern-tab active">All Items</a>
            <a href="#" class="modern-tab">Active</a>
            <a href="#" class="modern-tab">Inactive</a>
        </div>

        {{-- Modern Data Table Card --}}
        <div class="modern-card">
            <div class="modern-card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-1">Items List</h3>
                    </div>
                    <div class="modern-search">
                        <i class="bi bi-search modern-search-icon"></i>
                        <input type="text" class="modern-input modern-search-input" placeholder="Search...">
                    </div>
                </div>
            </div>
            <div class="modern-card-body p-0">
                <div class="table-responsive">
                    <table class="modern-table datatable" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Column 1</th>
                                <th>Column 2</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- FOR FORM PAGES: Use this structure --}}
        <div class="modern-card">
            <div class="modern-card-header">
                <h3>Form Title</h3>
            </div>
            <div class="modern-card-body">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Field Name</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-3 mt-4">
                        <a href="#" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- FOR DETAIL PAGES: Use this structure --}}
        <div class="modern-card">
            <div class="modern-card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3>Item Details</h3>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <a href="#" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash me-1"></i>Delete
                        </a>
                    </div>
                </div>
            </div>
            <div class="modern-card-body">
                {{-- Detail content here --}}
            </div>
        </div>

    </div> {{-- Close modern-content --}}
</div> {{-- Close modern-container --}}
@endsection

{{-- IMPORTANT RULES FOR ALL PAGES:
1. Always use standard Bootstrap buttons (btn btn-primary) not custom ones
2. Always wrap content in modern-content div
3. Always use modern-card for content areas
4. Always use consistent header structure
5. Always include breadcrumb
6. Always close divs properly
7. Use same color scheme: text-primary for icons
8. Use same spacing and layout
--}}
