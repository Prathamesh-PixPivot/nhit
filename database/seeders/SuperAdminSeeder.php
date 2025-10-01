<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create superadmin user
        $superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('admin123'),
            'email_verified_at' => now(),
        ]);

        // Create regular user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@test.com',
            'password' => bcrypt('user123'),
            'email_verified_at' => now(),
        ]);

        // Create roles
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Create permissions
        $permissions = [
            'view-dashboard',
            'manage-users',
            'manage-roles',
            'manage-permissions',
            'view-reports',
            'manage-settings',
            'view-payments',
            'manage-payments',
            'view-notes',
            'manage-notes',
            'view-tickets',
            'manage-tickets',
            'view-accounts',
            'manage-accounts',
            'view-vendors',
            'manage-vendors',
            'view-beneficiaries',
            'manage-beneficiaries',
            'view-approvals',
            'manage-approvals'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to superadmin
        $superadminRole->syncPermissions(Permission::all());

        // Assign limited permissions to admin
        $adminPermissions = Permission::whereIn('name', [
            'view-dashboard',
            'view-reports',
            'view-payments',
            'view-notes',
            'view-tickets',
            'view-accounts',
            'view-vendors',
            'view-beneficiaries',
            'view-approvals'
        ])->get();
        $adminRole->syncPermissions($adminPermissions);

        // Assign basic permissions to user
        $userPermissions = Permission::whereIn('name', [
            'view-dashboard',
            'view-payments',
            'view-notes',
            'view-tickets'
        ])->get();
        $userRole->syncPermissions($userPermissions);

        // Assign roles to users
        $superadmin->assignRole('superadmin');
        $admin->assignRole('admin');
        $user->assignRole('user');

        $this->command->info('Test users created successfully!');
        $this->command->info('Superadmin: superadmin@test.com / password123');
        $this->command->info('Admin: admin@test.com / admin123');
        $this->command->info('User: user@test.com / user123');
    }
}
