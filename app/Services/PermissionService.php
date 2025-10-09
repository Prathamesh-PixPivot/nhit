<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Config;

class PermissionService
{
    /**
     * Check if user has permission for a specific action on a model
     */
    public function hasPermission(User $user, string $model, string $action): bool
    {
        // SuperAdmin has all permissions
        $superAdminRole = Config::get('draft_permissions.superadmin_role', 'Super Admin');
        if ($user->hasRole($superAdminRole)) {
            return true;
        }

        // Get permissions for the model
        $permissions = Config::get("draft_permissions.{$model}_permissions.{$action}", []);
        
        if (empty($permissions)) {
            return false;
        }

        return $user->hasAnyRole($permissions);
    }

    /**
     * Check payment note permissions
     */
    public function canViewAllPaymentNotes(User $user): bool
    {
        return $this->hasPermission($user, 'payment_note', 'view_all');
    }

    public function canCreatePaymentNote(User $user): bool
    {
        return $this->hasPermission($user, 'payment_note', 'create');
    }

    public function canEditPaymentNote(User $user): bool
    {
        return $this->hasPermission($user, 'payment_note', 'edit');
    }

    public function canDeletePaymentNote(User $user): bool
    {
        return $this->hasPermission($user, 'payment_note', 'delete');
    }

    public function canApprovePaymentNote(User $user): bool
    {
        return $this->hasPermission($user, 'payment_note', 'approve');
    }

    /**
     * Check green note permissions
     */
    public function canViewAllGreenNotes(User $user): bool
    {
        return $this->hasPermission($user, 'green_note', 'view_all');
    }

    public function canCreateGreenNote(User $user): bool
    {
        return $this->hasPermission($user, 'green_note', 'create');
    }

    public function canEditGreenNote(User $user): bool
    {
        return $this->hasPermission($user, 'green_note', 'edit');
    }

    public function canDeleteGreenNote(User $user): bool
    {
        return $this->hasPermission($user, 'green_note', 'delete');
    }

    public function canApproveGreenNote(User $user): bool
    {
        return $this->hasPermission($user, 'green_note', 'approve');
    }

    public function canHoldGreenNote(User $user): bool
    {
        return $this->hasPermission($user, 'green_note', 'hold');
    }

    /**
     * Check vendor permissions
     */
    public function canViewAllVendors(User $user): bool
    {
        return $this->hasPermission($user, 'vendor', 'view_all');
    }

    public function canCreateVendor(User $user): bool
    {
        return $this->hasPermission($user, 'vendor', 'create');
    }

    public function canEditVendor(User $user): bool
    {
        return $this->hasPermission($user, 'vendor', 'edit');
    }

    public function canDeleteVendor(User $user): bool
    {
        return $this->hasPermission($user, 'vendor', 'delete');
    }

    public function canManageVendorAccounts(User $user): bool
    {
        return $this->hasPermission($user, 'vendor', 'manage_accounts');
    }

    /**
     * Check draft edit permissions
     */
    public function canEditDrafts(User $user): bool
    {
        $superAdminRole = Config::get('draft_permissions.superadmin_role', 'Super Admin');
        if ($user->hasRole($superAdminRole)) {
            return true;
        }

        $draftEditRoles = Config::get('draft_permissions.draft_edit_roles', []);
        return $user->hasAnyRole($draftEditRoles);
    }

    /**
     * Get all configurable roles for a specific permission
     */
    public function getRolesForPermission(string $model, string $action): array
    {
        return Config::get("draft_permissions.{$model}_permissions.{$action}", []);
    }

    /**
     * Get all draft edit roles
     */
    public function getDraftEditRoles(): array
    {
        return Config::get('draft_permissions.draft_edit_roles', []);
    }

    /**
     * Update permissions configuration (for admin interface)
     */
    public function updatePermissions(string $model, string $action, array $roles): bool
    {
        try {
            // This would typically update a database table or config file
            // For now, we'll just validate the input
            if (empty($model) || empty($action) || !is_array($roles)) {
                return false;
            }

            // In a real implementation, you might:
            // 1. Update database table
            // 2. Update config file
            // 3. Clear cache
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
