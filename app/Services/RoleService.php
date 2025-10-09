<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;

class RoleService
{
    /**
     * Get all approval-related roles that exist in the system
     */
    public static function getApprovalRoles(): Collection
    {
        return Role::where(function ($query) {
            $query->where('name', 'like', '%approver%')
                  ->orWhere('name', 'like', '%approval%')
                  ->orWhere('name', 'like', '%reviewer%')
                  ->orWhere('name', 'like', '%QS%')
                  ->orWhere('name', 'like', '%GN%')
                  ->orWhere('name', 'like', '%ER%')
                  ->orWhere('name', 'like', '%PN%')
                  ->orWhere('name', 'like', '%manager%')
                  ->orWhere('name', 'like', '%admin%');
        })->get();
    }
    
    /**
     * Get users with approval-related roles
     */
    public static function getUsersWithApprovalRoles(): Collection
    {
        $approvalRoles = self::getApprovalRoles();
        
        if ($approvalRoles->isEmpty()) {
            // If no approval roles exist, return empty collection
            return collect();
        }
        
        $roleNames = $approvalRoles->pluck('name')->toArray();
        
        try {
            return User::role($roleNames)->get();
        } catch (\Exception $e) {
            // If any role doesn't exist, get users individually
            $users = collect();
            foreach ($roleNames as $roleName) {
                try {
                    $roleUsers = User::role($roleName)->get();
                    $users = $users->merge($roleUsers);
                } catch (\Exception $roleError) {
                    // Skip this role if it doesn't exist
                    continue;
                }
            }
            return $users->unique('id');
        }
    }
    
    /**
     * Get all existing roles safely
     */
    public static function getAllRoles(): Collection
    {
        return Role::all();
    }
    
    /**
     * Check if a role exists
     */
    public static function roleExists(string $roleName): bool
    {
        return Role::where('name', $roleName)->exists();
    }
    
    /**
     * Get users with specific roles (safely)
     */
    public static function getUsersWithRoles(array $roleNames): Collection
    {
        $existingRoles = Role::whereIn('name', $roleNames)->pluck('name')->toArray();
        
        if (empty($existingRoles)) {
            return collect();
        }
        
        try {
            return User::role($existingRoles)->get();
        } catch (\Exception $e) {
            // Fallback: get users for each role individually
            $users = collect();
            foreach ($existingRoles as $roleName) {
                try {
                    $roleUsers = User::role($roleName)->get();
                    $users = $users->merge($roleUsers);
                } catch (\Exception $roleError) {
                    continue;
                }
            }
            return $users->unique('id');
        }
    }
    
    /**
     * Create default roles if they don't exist
     */
    public static function createDefaultRoles(): void
    {
        $defaultRoles = [
            'superadmin' => 'Super Administrator',
            'admin' => 'Administrator', 
            'manager' => 'Manager',
            'user' => 'Regular User',
            'approver' => 'General Approver',
            'reviewer' => 'Reviewer'
        ];
        
        foreach ($defaultRoles as $name => $description) {
            if (!self::roleExists($name)) {
                Role::create([
                    'name' => $name,
                    'guard_name' => 'web'
                ]);
            }
        }
    }
}
