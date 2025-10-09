<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            // Find the first SuperAdmin user or create one
            $superAdmin = User::role('superadmin')->first();
            if (!$superAdmin) {
                $this->command->error('No SuperAdmin user found. Please create a SuperAdmin user first.');
                return;
            }

            // Check if default organization already exists
            $existingOrg = Organization::where('code', 'NHIT')->first();
            if ($existingOrg) {
                $this->command->info('Default organization already exists.');
                return;
            }

            // Create default organization
            $defaultOrg = Organization::create([
                'name' => 'NHIT Default Organization',
                'code' => 'NHIT',
                'database_name' => config('database.connections.mysql.database'),
                'description' => 'Default organization for existing users and data',
                'is_active' => true,
                'created_by' => $superAdmin->id,
            ]);

            // Update all existing users to belong to the default organization
            User::whereNull('organization_id')->update([
                'organization_id' => $defaultOrg->id,
                'current_organization_id' => $defaultOrg->id
            ]);

            // Store main database name in config for future reference
            config(['app.main_database' => config('database.connections.mysql.database')]);

            DB::commit();

            $this->command->info('Default organization created and users updated successfully!');
            $this->command->info("Organization ID: {$defaultOrg->id}");
            $this->command->info("Organization Code: {$defaultOrg->code}");
            $this->command->info("Database: {$defaultOrg->database_name}");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Failed to create default organization: ' . $e->getMessage());
        }
    }
}
