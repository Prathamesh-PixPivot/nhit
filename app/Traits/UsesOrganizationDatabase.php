<?php

namespace App\Traits;

/**
 * Trait for models that should use organization-specific database
 * (Isolated data per organization)
 */
trait UsesOrganizationDatabase
{
    /**
     * Get the current connection name for the model.
     * Always returns the organization connection
     */
    public function getConnectionName()
    {
        return 'organization';
    }
    
    /**
     * Get the database connection for the model.
     * Always uses the organization database connection
     */
    public function getConnection()
    {
        return \DB::connection('organization');
    }
}
