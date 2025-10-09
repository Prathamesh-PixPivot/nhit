<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\UsesSharedDatabase;

class Organization extends Model
{
    use HasFactory, UsesSharedDatabase;

    /**
     * The connection name for the model.
     * Always use main database for organizations
     *
     * @var string
     */
    protected $connection = 'mysql';

    protected $fillable = [
        'name',
        'code',
        'database_name',
        'description',
        'logo',
        'settings',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get the user who created this organization
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all users belonging to this organization
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope to get only active organizations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get organizations accessible by a specific user (superadmin sees all)
     */
    public function scopeAccessibleBy($query, User $user)
    {
        if ($user->hasRole('superadmin')) {
            return $query->active();
        }
        
        return $query->where('created_by', $user->id)->active();
    }

    /**
     * Generate unique database name for organization
     */
    public static function generateDatabaseName(string $orgCode): string
    {
        $baseName = 'nhit_' . strtolower($orgCode);
        $counter = 1;
        $databaseName = $baseName;
        
        while (static::where('database_name', $databaseName)->exists()) {
            $databaseName = $baseName . '_' . $counter;
            $counter++;
        }
        
        return $databaseName;
    }

    /**
     * Create database for this organization
     */
    public function createDatabase(): bool
    {
        try {
            \DB::statement("CREATE DATABASE IF NOT EXISTS `{$this->database_name}`");
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to create database for organization {$this->name}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clone structure from main database to organization database
     */
    public function cloneDatabaseStructure(): bool
    {
        try {
            $mainDatabase = config('database.connections.mysql.database');
            
            \Log::info("Starting database structure clone from {$mainDatabase} to {$this->database_name}");
            
            // Get all tables from main database
            $tables = \DB::select("SHOW TABLES");
            
            \Log::info("Found " . count($tables) . " tables to clone");
            
            $clonedCount = 0;
            $skippedCount = 0;
            $failedCount = 0;
            
            // Get shared tables from config (these should NOT be cloned)
            $sharedTables = config('multitenancy.shared_tables', [
                'migrations', 'organizations', 'users', 'roles', 'permissions',
                'model_has_roles', 'model_has_permissions', 'role_has_permissions',
                'vendors', 'vendor_accounts', 'departments', 'designations',
                'user_login_histories', 'user_logs', 'password_reset_tokens',
                'cache', 'cache_locks', 'sessions', 'failed_jobs', 'jobs', 'job_batches',
                'messages', 'conversations', 'folders', 'labels', 'folder_message', 'label_message',
                'tickets', 'ticket_comments', 'ticket_status_logs', 'notifications', 'activity_log'
            ]);
            
            $skippedTables = $sharedTables;
            
            foreach ($tables as $table) {
                $tableName = array_values((array)$table)[0];
                
                // Skip shared tables (these remain in main database)
                if (in_array($tableName, $skippedTables)) {
                    \Log::info("Skipping shared table: {$tableName}");
                    $skippedCount++;
                    continue;
                }
                
                try {
                    // Check if table already exists in target database
                    \DB::statement("USE `{$this->database_name}`");
                    $existingTables = \DB::select("SHOW TABLES LIKE '{$tableName}'");
                    
                    if (!empty($existingTables)) {
                        \Log::info("Table already exists, skipping: {$tableName}");
                        \DB::statement("USE `{$mainDatabase}`");
                        $skippedCount++;
                        continue;
                    }
                    
                    // Switch back to main database to get structure
                    \DB::statement("USE `{$mainDatabase}`");
                    
                    // Get table structure using SHOW CREATE TABLE
                    $createTableResult = \DB::select("SHOW CREATE TABLE `{$tableName}`");
                    
                    if (empty($createTableResult)) {
                        \Log::warning("Could not get CREATE statement for table: {$tableName}");
                        $failedCount++;
                        continue;
                    }
                    
                    $createStatement = $createTableResult[0]->{'Create Table'};
                    
                    // Switch to organization database and create table
                    \DB::statement("USE `{$this->database_name}`");
                    
                    // Disable foreign key checks temporarily
                    \DB::statement('SET FOREIGN_KEY_CHECKS=0');
                    \DB::statement($createStatement);
                    \DB::statement('SET FOREIGN_KEY_CHECKS=1');
                    
                    // Switch back to main database
                    \DB::statement("USE `{$mainDatabase}`");
                    
                    $clonedCount++;
                    \Log::info("Successfully cloned table: {$tableName}");
                    
                } catch (\Exception $tableError) {
                    \Log::error("Failed to clone table {$tableName}: " . $tableError->getMessage());
                    $failedCount++;
                    
                    // Ensure we're back on main database
                    try {
                        \DB::statement("USE `{$mainDatabase}`");
                    } catch (\Exception $switchError) {
                        // Ignore
                    }
                }
            }
            
            // Ensure we're back on main database
            \DB::statement("USE `{$mainDatabase}`");
            
            \Log::info("Database cloning complete for {$this->database_name}:");
            \Log::info("- Successfully cloned (isolated): {$clonedCount} tables");
            \Log::info("- Skipped (shared/existing): {$skippedCount} tables");
            \Log::info("- Failed: {$failedCount} tables");
            \Log::info("Note: Shared tables (users, roles, vendors, etc.) remain in main database");
            
            // Return true if at least some tables were cloned
            return $clonedCount > 0;
            
        } catch (\Exception $e) {
            \Log::error("Failed to clone database structure for organization {$this->name}: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            
            // Ensure we switch back to main database
            try {
                $mainDatabase = config('database.connections.mysql.database');
                \DB::statement("USE `{$mainDatabase}`");
            } catch (\Exception $switchError) {
                \Log::error("Failed to switch back to main database: " . $switchError->getMessage());
            }
            
            return false;
        }
    }
}
