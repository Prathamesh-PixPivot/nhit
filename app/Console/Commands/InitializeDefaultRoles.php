<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RoleService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class InitializeDefaultRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nhit:init-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize default roles and permissions for NHIT application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Initializing default roles and permissions...');
        
        // Create default roles
        $defaultRoles = [
            'superadmin' => 'Super Administrator - Full system access',
            'admin' => 'Administrator - System management',
            'manager' => 'Manager - Department management',
            'approver' => 'General Approver - Approval workflows',
            'reviewer' => 'Reviewer - Review and feedback',
            'user' => 'Regular User - Basic access',
            
            // Legacy role mappings for backward compatibility
            'GN Approver' => 'Green Note Approver',
            'ER Approver' => 'Expense Report Approver', 
            'PN Approver' => 'Payment Note Approver',
            'QS' => 'Quality Assurance',
            'Hr And Admin' => 'HR and Administration',
            'Auditor' => 'Auditor',
            'Qs' => 'Quality Assurance',
        ];
        
        $createdRoles = 0;
        $existingRoles = 0;
        
        foreach ($defaultRoles as $name => $description) {
            if (!Role::where('name', $name)->exists()) {
                Role::create([
                    'name' => $name,
                    'guard_name' => 'web'
                ]);
                $this->info("✅ Created role: {$name}");
                $createdRoles++;
            } else {
                $this->warn("⚠️  Role already exists: {$name}");
                $existingRoles++;
            }
        }
        
        // Create default permissions
        $defaultPermissions = [
            // User Management
            'view-users', 'create-users', 'edit-users', 'delete-users',
            
            // Role Management
            'view-roles', 'create-roles', 'edit-roles', 'delete-roles',
            
            // Green Notes
            'view-green-notes', 'create-green-notes', 'edit-green-notes', 'delete-green-notes', 'approve-green-notes',
            
            // Payment Notes
            'view-payment-notes', 'create-payment-notes', 'edit-payment-notes', 'delete-payment-notes', 'approve-payment-notes',
            
            // Reimbursement Notes
            'view-reimbursement-notes', 'create-reimbursement-notes', 'edit-reimbursement-notes', 'delete-reimbursement-notes', 'approve-reimbursement-notes',
            
            // Vendors
            'view-vendors', 'create-vendors', 'edit-vendors', 'delete-vendors',
            
            // Departments
            'view-departments', 'create-departments', 'edit-departments', 'delete-departments',
            
            // Organizations
            'view-organizations', 'create-organizations', 'edit-organizations', 'delete-organizations',
        ];
        
        $createdPermissions = 0;
        $existingPermissions = 0;
        
        foreach ($defaultPermissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create([
                    'name' => $permission,
                    'guard_name' => 'web'
                ]);
                $createdPermissions++;
            } else {
                $existingPermissions++;
            }
        }
        
        // Assign permissions to superadmin role
        $superAdminRole = Role::where('name', 'superadmin')->first();
        if ($superAdminRole) {
            $allPermissions = Permission::all();
            $superAdminRole->syncPermissions($allPermissions);
            $this->info("✅ Assigned all permissions to superadmin role");
        }
        
        $this->newLine();
        $this->info("=== Summary ===");
        $this->info("Roles created: {$createdRoles}");
        $this->info("Roles existing: {$existingRoles}");
        $this->info("Permissions created: {$createdPermissions}");
        $this->info("Permissions existing: {$existingPermissions}");
        $this->newLine();
        $this->info("✅ Default roles and permissions initialized successfully!");
        
        return 0;
    }
}
