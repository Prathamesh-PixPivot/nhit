<?php

namespace App\Console\Commands;

use App\Models\Organization;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CloneOrganizationDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nhit:clone-org-database {organization_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone database structure to organization database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $organizationId = $this->argument('organization_id');
        
        if ($organizationId) {
            $organizations = Organization::where('id', $organizationId)->get();
            if ($organizations->isEmpty()) {
                $this->error("Organization with ID {$organizationId} not found.");
                return 1;
            }
        } else {
            $organizations = Organization::all();
        }
        
        if ($organizations->isEmpty()) {
            $this->error('No organizations found.');
            return 1;
        }
        
        $this->info("Found {$organizations->count()} organization(s) to process");
        
        foreach ($organizations as $organization) {
            $this->info("\n=== Processing: {$organization->name} ===");
            $this->info("Database: {$organization->database_name}");
            
            // Create database if not exists
            if (!$this->databaseExists($organization->database_name)) {
                $this->info("Creating database...");
                if ($organization->createDatabase()) {
                    $this->info("✅ Database created");
                } else {
                    $this->error("❌ Failed to create database");
                    continue;
                }
            } else {
                $this->info("Database already exists");
            }
            
            // Clone structure
            $this->info("Cloning database structure...");
            $mainDatabase = config('database.connections.mysql.database');
            
            try {
                // Get all tables
                $tables = DB::select("SHOW TABLES");
                $totalTables = count($tables);
                $this->info("Found {$totalTables} tables to clone");
                
                $bar = $this->output->createProgressBar($totalTables);
                $bar->start();
                
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
                $failedTablesList = [];
                
                foreach ($tables as $table) {
                    $tableName = array_values((array)$table)[0];
                    
                    // Skip shared tables (these remain in main database)
                    if (in_array($tableName, $skippedTables)) {
                        $skippedCount++;
                        $bar->advance();
                        continue;
                    }
                    
                    try {
                        // Check if table already exists in target database
                        DB::statement("USE `{$organization->database_name}`");
                        $existingTables = DB::select("SHOW TABLES LIKE '{$tableName}'");
                        
                        if (!empty($existingTables)) {
                            // Table exists, skip
                            DB::statement("USE `{$mainDatabase}`");
                            $skippedCount++;
                            $bar->advance();
                            continue;
                        }
                        
                        // Switch back to main database to get structure
                        DB::statement("USE `{$mainDatabase}`");
                        $createTableResult = DB::select("SHOW CREATE TABLE `{$tableName}`");
                        
                        if (empty($createTableResult)) {
                            $this->warn("\nCould not get CREATE statement for table: {$tableName}");
                            $failedCount++;
                            $failedTablesList[] = $tableName;
                            $bar->advance();
                            continue;
                        }
                        
                        $createStatement = $createTableResult[0]->{'Create Table'};
                        
                        // Create table in organization database
                        DB::statement("USE `{$organization->database_name}`");
                        
                        // Disable foreign key checks temporarily
                        DB::statement('SET FOREIGN_KEY_CHECKS=0');
                        DB::statement($createStatement);
                        DB::statement('SET FOREIGN_KEY_CHECKS=1');
                        
                        // Switch back to main database
                        DB::statement("USE `{$mainDatabase}`");
                        
                        $clonedCount++;
                        
                    } catch (\Exception $tableError) {
                        $this->warn("\nFailed to clone table {$tableName}: " . $tableError->getMessage());
                        $failedCount++;
                        $failedTablesList[] = $tableName;
                        
                        // Ensure we're back on main database
                        try {
                            DB::statement("USE `{$mainDatabase}`");
                        } catch (\Exception $switchError) {
                            // Ignore
                        }
                    }
                    
                    $bar->advance();
                }
                
                $bar->finish();
                $this->newLine(2);
                
                // Switch back to main database
                DB::statement("USE `{$mainDatabase}`");
                
                // Display summary
                $this->info("=== Cloning Summary ===");
                $this->info("✅ Successfully cloned (isolated): {$clonedCount} tables");
                $this->info("⏭️  Skipped (shared/existing): {$skippedCount} tables");
                $this->info("ℹ️  Note: Shared tables (users, roles, vendors, etc.) remain in main database");
                
                if ($failedCount > 0) {
                    $this->warn("❌ Failed: {$failedCount} tables");
                    if (!empty($failedTablesList)) {
                        $this->warn("Failed tables: " . implode(', ', $failedTablesList));
                    }
                } else {
                    $this->info("❌ Failed: 0 tables");
                }
                
                // Calculate expected isolated tables
                $isolatedTables = config('multitenancy.isolated_tables', []);
                $expectedClone = count($isolatedTables) > 0 ? count($isolatedTables) : ($totalTables - count($skippedTables));
                
                if ($clonedCount >= $expectedClone || ($clonedCount + $skippedCount - count($skippedTables)) >= $expectedClone) {
                    $this->info("\n✅ All isolated tables successfully migrated!");
                    $this->info("Shared tables (users, roles, vendors, departments, etc.) remain in main database.");
                } else {
                    $missing = $expectedClone - $clonedCount;
                    $this->warn("\n⚠️  {$missing} isolated tables may be missing. Please check logs.");
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Failed to clone database structure: " . $e->getMessage());
                
                // Ensure we're back on main database
                try {
                    DB::statement("USE `{$mainDatabase}`");
                } catch (\Exception $switchError) {
                    // Ignore
                }
            }
        }
        
        $this->info("\n✅ Database cloning completed!");
        return 0;
    }
    
    /**
     * Check if database exists
     */
    protected function databaseExists(string $databaseName): bool
    {
        try {
            $databases = DB::select("SHOW DATABASES LIKE '{$databaseName}'");
            return !empty($databases);
        } catch (\Exception $e) {
            return false;
        }
    }
}
