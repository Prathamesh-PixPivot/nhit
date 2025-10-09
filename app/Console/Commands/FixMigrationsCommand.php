<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixMigrationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrations:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix migration conflicts by marking existing tables as migrated';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing migration conflicts...');

        // List of tables and their corresponding migrations
        $tableMigrations = [
            'comments' => '2025_02_18_000000_create_comments_table',
            'priorities' => '2025_03_11_111658_create_priorities_table',
            // Add more as needed
        ];

        $batch = DB::table('migrations')->max('batch') ?? 0;
        $batch++;

        foreach ($tableMigrations as $table => $migration) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                $this->info("Table '{$table}' exists, checking migration...");
                
                $exists = DB::table('migrations')
                    ->where('migration', $migration)
                    ->exists();
                
                if (!$exists) {
                    DB::table('migrations')->insert([
                        'migration' => $migration,
                        'batch' => $batch
                    ]);
                    
                    $this->info("✓ Marked '{$migration}' as run");
                } else {
                    $this->info("✓ Migration '{$migration}' already marked");
                }
            }
        }

        $this->info('Migration conflicts fixed!');
        $this->newLine();
        $this->info('You can now run: php artisan migrate');

        return 0;
    }
}
