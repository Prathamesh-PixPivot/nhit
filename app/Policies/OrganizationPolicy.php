<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    /**
     * Determine whether the user can view any organizations.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('superadmin');
    }

    /**
     * Determine whether the user can view the organization.
     */
    public function view(User $user, Organization $organization): bool
    {
        return $user->hasRole('superadmin') || $organization->created_by === $user->id;
    }

    /**
     * Determine whether the user can create organizations.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('superadmin');
    }

    /**
     * Determine whether the user can update the organization.
     */
    public function update(User $user, Organization $organization): bool
    {
        return $user->hasRole('superadmin') || $organization->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the organization.
     */
    public function delete(User $user, Organization $organization): bool
    {
        return $user->hasRole('superadmin') || $organization->created_by === $user->id;
    }

    /**
     * Determine whether the user can switch to this organization.
     */
    public function switch(User $user, Organization $organization): bool
    {
        if (!$organization->is_active) {
            return false;
        }

        return $user->hasRole('superadmin') || $organization->created_by === $user->id;
    }
}
