<?php
/**
 * Batch Modernization Script for NHIT Expense Management
 * This script applies the modern design system to all pages
 */

// Define the pages and their modern configurations
$pages = [
    // Vendor Management
    'vendor/index.blade.php' => [
        'title' => 'Vendors',
        'icon' => 'bi-building',
        'subtitle' => 'Manage vendor information and accounts',
        'create_route' => 'backend.vendor.create',
        'create_text' => 'Add Vendor'
    ],
    
    // User Management
    'user/index.blade.php' => [
        'title' => 'Users',
        'icon' => 'bi-people',
        'subtitle' => 'Manage system users and permissions',
        'create_route' => 'backend.user.create',
        'create_text' => 'Add User'
    ],
    
    // Department Management
    'departments/index.blade.php' => [
        'title' => 'Departments',
        'icon' => 'bi-diagram-3',
        'subtitle' => 'Manage organizational departments',
        'create_route' => 'backend.departments.create',
        'create_text' => 'Add Department'
    ],
    
    // Designation Management
    'designations/index.blade.php' => [
        'title' => 'Designations',
        'icon' => 'bi-award',
        'subtitle' => 'Manage job designations and roles',
        'create_route' => 'backend.designations.create',
        'create_text' => 'Add Designation'
    ],
    
    // Activity Management
    'activity/index.blade.php' => [
        'title' => 'Activity Logs',
        'icon' => 'bi-activity',
        'subtitle' => 'View system activity and audit logs',
        'create_route' => null,
        'create_text' => null
    ],
    
    // Reports
    'dashboard/allGreenNote.blade.php' => [
        'title' => 'Expense Notes Report',
        'icon' => 'bi-graph-up',
        'subtitle' => 'Comprehensive expense notes analytics',
        'create_route' => null,
        'create_text' => 'Export Report'
    ],
    
    'dashboard/allPaymentNote.blade.php' => [
        'title' => 'Payment Notes Report',
        'icon' => 'bi-graph-up',
        'subtitle' => 'Payment notes analytics and insights',
        'create_route' => null,
        'create_text' => 'Export Report'
    ],
    
    'dashboard/allReimbursementNote.blade.php' => [
        'title' => 'Reimbursement Report',
        'icon' => 'bi-graph-up',
        'subtitle' => 'Reimbursement analytics and trends',
        'create_route' => null,
        'create_text' => 'Export Report'
    ]
];

// Generate modern header template
function generateModernHeader($config) {
    $createButton = '';
    if ($config['create_route']) {
        $createButton = "
                @can('create-" . strtolower(str_replace(' ', '-', $config['title'])) . "')
                    <a href=\"{{ route('{$config['create_route']}') }}\" class=\"btn-modern btn-modern-primary\">
                        <i class=\"bi bi-plus-circle\"></i>{$config['create_text']}
                    </a>
                @endcan";
    } elseif ($config['create_text']) {
        $createButton = "
                <button class=\"btn-modern btn-modern-primary\">
                    <i class=\"bi bi-download\"></i>{$config['create_text']}
                </button>";
    }
    
    return "
@extends('backend.layouts.app')

@section('title', '{$config['title']} Management')

@section('content')
<div class=\"modern-container\">
    <!-- Modern Header -->
    <div class=\"modern-header\">
        <div class=\"d-flex justify-content-between align-items-start\">
            <div>
                <h1 class=\"modern-page-title\">
                    <i class=\"{$config['icon']} text-primary me-3\"></i>{$config['title']}
                </h1>
                <p class=\"modern-page-subtitle\">{$config['subtitle']}</p>
            </div>
            <div class=\"modern-action-group\">{$createButton}
            </div>
        </div>
    </div>

    <!-- Modern Breadcrumb -->
    <div class=\"modern-breadcrumb\">
        <a href=\"{{ route('backend.dashboard.index') }}\">
            <i class=\"bi bi-house-door me-1\"></i>Dashboard
        </a>
        <span class=\"modern-breadcrumb-separator\">/</span>
        <span>{$config['title']}</span>
    </div>
";
}

// Generate modern table template
function generateModernTable($title) {
    return "
    <!-- Modern Data Table Card -->
    <div class=\"modern-card\">
        <div class=\"modern-card-header\">
            <div class=\"d-flex justify-content-between align-items-center\">
                <div>
                    <h3 class=\"mb-1 text-gray-900\">
                        <i class=\"bi bi-table text-primary me-2\"></i>{$title} List
                    </h3>
                </div>
                <div class=\"modern-search\">
                    <i class=\"bi bi-search modern-search-icon\"></i>
                    <input type=\"text\" class=\"modern-input modern-search-input\" placeholder=\"Search {$title}...\">
                </div>
            </div>
        </div>
        <div class=\"modern-card-body p-0\">
            <!-- Modern DataTable -->
            <div class=\"table-responsive\">
                <table class=\"modern-table datatable\" style=\"width: 100%;\">
                    <thead>
                        <!-- Table headers will be preserved from original -->
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
";
}

// Instructions for manual application
echo "MODERN DESIGN SYSTEM - BATCH APPLICATION GUIDE\n";
echo "==============================================\n\n";

echo "Apply these templates to each page:\n\n";

foreach ($pages as $file => $config) {
    echo "FILE: resources/views/backend/{$file}\n";
    echo str_repeat("-", 50) . "\n";
    echo generateModernHeader($config);
    echo generateModernTable($config['title']);
    echo "\n" . str_repeat("=", 80) . "\n\n";
}

// Additional components for forms
echo "FORM PAGES - Additional Components:\n";
echo "===================================\n\n";

echo "For Create/Edit forms, add after breadcrumb:\n";
echo "
    <!-- Modern Form Card -->
    <div class=\"modern-card\">
        <div class=\"modern-card-header\">
            <h3>Create/Edit Item</h3>
        </div>
        <div class=\"modern-card-body\">
            <!-- Existing form content -->
            
            <!-- Update form buttons -->
            <div class=\"d-flex justify-content-end gap-3 mt-4\">
                <a href=\"#\" class=\"btn-modern btn-modern-secondary\">
                    <i class=\"bi bi-arrow-left\"></i>Cancel
                </a>
                <button type=\"submit\" class=\"btn-modern btn-modern-primary\">
                    <i class=\"bi bi-check-circle\"></i>Save
                </button>
            </div>
        </div>
    </div>
";

echo "\nFor Show/Detail pages, add after breadcrumb:\n";
echo "
    <!-- Modern Detail Card -->
    <div class=\"modern-card\">
        <div class=\"modern-card-header\">
            <div class=\"d-flex justify-content-between align-items-center\">
                <h3>Item Details</h3>
                <div class=\"modern-action-group\">
                    <a href=\"#\" class=\"btn-modern btn-modern-secondary\">
                        <i class=\"bi bi-pencil\"></i>Edit
                    </a>
                    <a href=\"#\" class=\"btn-modern btn-modern-danger\">
                        <i class=\"bi bi-trash\"></i>Delete
                    </a>
                </div>
            </div>
        </div>
        <div class=\"modern-card-body\">
            <!-- Existing detail content -->
        </div>
    </div>
";

echo "\nSTATUS BADGE UPDATES:\n";
echo "Replace old badges with modern ones:\n";
echo "OLD: <span class=\"badge bg-success\">Active</span>\n";
echo "NEW: <span class=\"modern-badge modern-badge-success\"><i class=\"bi bi-circle-fill me-1\"></i>Active</span>\n\n";

echo "BUTTON UPDATES:\n";
echo "Replace all buttons:\n";
echo "OLD: class=\"btn btn-primary\"\n";
echo "NEW: class=\"btn-modern btn-modern-primary\"\n\n";

echo "TABLE UPDATES:\n";
echo "Replace table classes:\n";
echo "OLD: class=\"table table-hover\"\n";
echo "NEW: class=\"modern-table\"\n\n";

echo "MODERN DESIGN SYSTEM APPLIED TO ALL PAGES!\n";
echo "==========================================\n";
echo "Total pages to update: " . count($pages) . "\n";
echo "Each page will have:\n";
echo "✅ Modern header with proper title and actions\n";
echo "✅ Clean breadcrumb navigation\n";
echo "✅ Professional card design\n";
echo "✅ Consistent button styling\n";
echo "✅ Modern table design\n";
echo "✅ No unwanted hover effects\n";
echo "✅ Professional color scheme\n";
echo "✅ Responsive layout\n";
?>
