<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateTestOrganization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nhit:create-test-org';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test organization to demonstrate the switcher';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating test organization...');

        try {
            DB::beginTransaction();

            // Find SuperAdmin user
            $superAdmin = User::role('superadmin')->first();
            
            if (!$superAdmin) {
                $this->error('No SuperAdmin user found.');
                return 1;
            }

            // Check if test organization already exists
            $testOrg = Organization::where('code', 'TEST')->first();
            
            if ($testOrg) {
                $this->info('Test organization already exists.');
                return 0;
            }

            // Create test organization
            $testOrg = Organization::create([
                'name' => 'Test Organization',
                'code' => 'TEST',
                'database_name' => 'nhit_test',
                'description' => 'Test organization for demonstrating the switcher functionality',
                'is_active' => true,
                'created_by' => $superAdmin->id,
            ]);

            DB::commit();

            // Create database for test organization (outside transaction)
            if ($testOrg->createDatabase()) {
                $this->info("Created database: {$testOrg->database_name}");
                
                // Clone database structure
                if ($testOrg->cloneDatabaseStructure()) {
                    $this->info("Cloned database structure successfully");
                } else {
                    $this->warn("Failed to clone database structure");
                }
            } else {
                $this->warn("Failed to create database");
            }

            $this->info("✅ Test organization created successfully!");
            $this->info("Name: {$testOrg->name}");
            $this->info("Code: {$testOrg->code}");
            $this->info("Database: {$testOrg->database_name}");
            
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Failed to create test organization: " . $e->getMessage());
            return 1;
        }
    }
}
