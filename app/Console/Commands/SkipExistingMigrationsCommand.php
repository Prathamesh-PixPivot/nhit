<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SkipExistingMigrationsCommand extends Command
{
    protected $signature = 'migrations:skip-existing';
    protected $description = 'Mark all migrations for existing tables as run';

    public function handle()
    {
        $this->info('Scanning for existing tables and pending migrations...');

        // Get all existing tables
        $tables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();
        $tableKey = "Tables_in_{$databaseName}";
        
        $existingTables = collect($tables)->pluck($tableKey)->toArray();
        
        $this->info('Found ' . count($existingTables) . ' existing tables');

        // Get all migration files
        $migrationPath = database_path('migrations');
        $migrationFiles = File::files($migrationPath);

        $batch = DB::table('migrations')->max('batch') ?? 0;
        $batch++;

        $marked = 0;

        foreach ($migrationFiles as $file) {
            $migrationName = str_replace('.php', '', $file->getFilename());
            
            // Check if migration is already run
            $alreadyRun = DB::table('migrations')
                ->where('migration', $migrationName)
                ->exists();
            
            if ($alreadyRun) {
                continue;
            }

            // Try to detect table name from migration file
            $tableName = $this->extractTableName($file->getPathname());
            
            if ($tableName && in_array($tableName, $existingTables)) {
                DB::table('migrations')->insert([
                    'migration' => $migrationName,
                    'batch' => $batch
                ]);
                
                $this->info("✓ Marked '{$migrationName}' as run (table '{$tableName}' exists)");
                $marked++;
            }
        }

        if ($marked > 0) {
            $this->info("\nMarked {$marked} migrations as run");
        } else {
            $this->info("\nNo migrations needed to be marked");
        }

        $this->newLine();
        $this->info('You can now run: php artisan migrate');

        return 0;
    }

    private function extractTableName($filePath)
    {
        $content = File::get($filePath);
        
        // Try to find create table statement
        if (preg_match("/Schema::create\(['\"]([^'\"]+)['\"]/", $content, $matches)) {
            return $matches[1];
        }
        
        // Try to find table modification statement
        if (preg_match("/Schema::table\(['\"]([^'\"]+)['\"]/", $content, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
}
