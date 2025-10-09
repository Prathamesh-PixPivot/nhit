<?php

namespace App\Traits;

/**
 * Trait for models that should always use the main database
 * (Shared data across all organizations)
 */
trait UsesSharedDatabase
{
    /**
     * Get the current connection name for the model.
     * Always returns the main mysql connection
     */
    public function getConnectionName()
    {
        return 'mysql';
    }
    
    /**
     * Get the database connection for the model.
     * Always uses the main database connection
     */
    public function getConnection()
    {
        return \DB::connection('mysql');
    }
}
