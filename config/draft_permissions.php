<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Draft Edit Permissions Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which roles can edit drafts and perform various operations
    |
    */

    'draft_edit_roles' => [
        'Super Admin',
        'Project Accounts Team',
        'Accounts Team', 
        'Finance Team',
        // Add more roles as needed
    ],

    'payment_note_permissions' => [
        'view_all' => [
            'Super Admin',
            'Finance Manager',
            'Accounts Head',
        ],
        'create' => [
            'Super Admin',
            'Accounts Team',
            'Project Accounts Team',
        ],
        'edit' => [
            'Super Admin',
            'Accounts Team',
            'Project Accounts Team',
        ],
        'delete' => [
            'Super Admin',
            'Accounts Head',
        ],
        'approve' => [
            'Super Admin',
            'Finance Manager',
            'Accounts Head',
        ],
    ],

    'green_note_permissions' => [
        'view_all' => [
            'Super Admin',
            'Finance Manager',
            'Department Head',
        ],
        'create' => [
            'Super Admin',
            'Department User',
            'Project Team',
        ],
        'edit' => [
            'Super Admin',
            'Department User',
            'Project Team',
        ],
        'delete' => [
            'Super Admin',
            'Department Head',
        ],
        'approve' => [
            'Super Admin',
            'Department Head',
            'Finance Manager',
        ],
        'hold' => [
            'Super Admin',
            'Finance Manager',
            'Accounts Head',
        ],
    ],

    'vendor_permissions' => [
        'view_all' => [
            'Super Admin',
            'Vendor Manager',
            'Accounts Team',
        ],
        'create' => [
            'Super Admin',
            'Vendor Manager',
            'Accounts Team',
        ],
        'edit' => [
            'Super Admin',
            'Vendor Manager',
            'Accounts Team',
        ],
        'delete' => [
            'Super Admin',
            'Vendor Manager',
        ],
        'manage_accounts' => [
            'Super Admin',
            'Vendor Manager',
            'Accounts Team',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SuperAdmin Full Access
    |--------------------------------------------------------------------------
    |
    | SuperAdmin role that has access to everything
    |
    */
    'superadmin_role' => 'Super Admin',

    /*
    |--------------------------------------------------------------------------
    | Department Based Access
    |--------------------------------------------------------------------------
    |
    | Enable department-based access control
    |
    */
    'enable_department_access' => true,

    /*
    |--------------------------------------------------------------------------
    | Creator Access
    |--------------------------------------------------------------------------
    |
    | Allow creators to edit their own records
    |
    */
    'allow_creator_access' => true,
];
