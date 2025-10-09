<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Multi-Tenancy Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration defines which tables are shared across all organizations
    | and which tables are isolated per organization.
    |
    */

    /**
     * Shared Tables - Common across all organizations
     * These tables remain in the main database and are accessed by all organizations
     */
    'shared_tables' => [
        // Core System Tables
        'migrations',
        'organizations',
        'cache',
        'cache_locks',
        'sessions',
        'failed_jobs',
        'jobs',
        'job_batches',
        
        // User Management (Shared)
        'users',
        'user_login_histories',
        'user_logs',
        'password_reset_tokens',
        
        // Role & Permission Management (Shared)
        'roles',
        'permissions',
        'model_has_roles',
        'model_has_permissions',
        'role_has_permissions',
        
        // Vendor Management (Shared)
        'vendors',
        'vendor_accounts',
        
        // Department & Designation (Shared)
        'departments',
        'designations',
        
        // Messaging System (Shared)
        'messages',
        'conversations',
        'folders',
        'labels',
        'folder_message',
        'label_message',
        
        // Ticketing System (Shared)
        'tickets',
        'ticket_comments',
        'ticket_status_logs',
        
        // Notifications (Shared)
        'notifications',
        
        // Activity Log (Shared)
        'activity_log',
    ],

    /**
     * Isolated Tables - Separate per organization
     * These tables are cloned to each organization's database
     */
    'isolated_tables' => [
        // Accounts
        'accounts',
        
        // Approval System
        'approval_flows',
        'approval_logs',
        'approval_steps',
        'priorities',
        
        // Green Notes (Expense Notes)
        'green_notes',
        'comments',
        'supporting_docs',
        
        // Payment Notes
        'payment_notes',
        'payment_note_approval_logs',
        'payment_note_approval_priorities',
        'payment_note_approval_steps',
        'payment_note_log_priority',
        
        // Bank Letters
        'bank_letter_approval_logs',
        'bank_letter_approval_priorities',
        'bank_letter_approval_steps',
        'bank_letter_log_priority',
        
        // Reimbursement Notes
        'reimbursement_notes',
        'reimbursement_expense_details',
        'reimbursement_approval_logs',
        
        // Payments
        'payments',
        'payments_new',
        'payments_shortcuts',
        'payments_shortcuts_new',
        
        // Products & Ratios
        'products',
        'ratios',
    ],

    /**
     * Models that should always use main database (shared data)
     */
    'shared_models' => [
        'App\Models\User',
        'App\Models\Organization',
        'App\Models\Vendor',
        'App\Models\VendorAccount',
        'App\Models\Department',
        'App\Models\Designation',
        'Spatie\Permission\Models\Role',
        'Spatie\Permission\Models\Permission',
    ],

    /**
     * Models that use organization-specific database (isolated data)
     */
    'isolated_models' => [
        'App\Models\GreenNote',
        'App\Models\PaymentNote',
        'App\Models\ReimbursementNote',
        'App\Models\Account',
        'App\Models\Product',
    ],
];
