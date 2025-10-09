<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupOrganizations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nhit:setup-organizations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup organization system with default organization';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up organization system...');

        try {
            DB::beginTransaction();

            // Find SuperAdmin users
            $superAdmins = User::role('superadmin')->get();
            
            if ($superAdmins->isEmpty()) {
                $this->error('No SuperAdmin users found. Please create a SuperAdmin user first.');
                return 1;
            }

            $this->info("Found {$superAdmins->count()} SuperAdmin user(s)");

            // Check if default organization exists
            $defaultOrg = Organization::where('code', 'NHIT')->first();
            
            if (!$defaultOrg) {
                // Create default organization
                $defaultOrg = Organization::create([
                    'name' => 'NHIT Default Organization',
                    'code' => 'NHIT',
                    'database_name' => config('database.connections.mysql.database'),
                    'description' => 'Default organization for existing users and data',
                    'is_active' => true,
                    'created_by' => $superAdmins->first()->id,
                ]);

                $this->info("Created default organization: {$defaultOrg->name}");
            } else {
                $this->info("Default organization already exists: {$defaultOrg->name}");
            }

            // Update users without organization
            $usersWithoutOrg = User::whereNull('organization_id')->count();
            if ($usersWithoutOrg > 0) {
                User::whereNull('organization_id')->update([
                    'organization_id' => $defaultOrg->id,
                    'current_organization_id' => $defaultOrg->id
                ]);
                $this->info("Updated {$usersWithoutOrg} users to belong to default organization");
            }

            // Display current status
            $this->info("\n=== Organization System Status ===");
            $this->info("Default Organization: {$defaultOrg->name} (ID: {$defaultOrg->id})");
            $this->info("Database: {$defaultOrg->database_name}");
            $this->info("Created by: {$defaultOrg->creator->name}");
            $this->info("Active: " . ($defaultOrg->is_active ? 'Yes' : 'No'));
            
            $totalUsers = User::count();
            $usersInOrg = User::where('organization_id', $defaultOrg->id)->count();
            $this->info("Users in organization: {$usersInOrg}/{$totalUsers}");

            DB::commit();
            $this->info("\n✅ Organization system setup completed successfully!");
            
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Failed to setup organization system: " . $e->getMessage());
            return 1;
        }
    }
}
