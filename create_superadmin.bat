@echo off
REM Create Superadmin Test User for Laravel Application
REM This script creates a superadmin user for testing purposes

echo Creating Superadmin Test User...

echo.
echo ========================================
echo Superadmin User Creation Started
echo ========================================

echo.
echo Step 1: Creating superadmin user via Laravel Tinker...
php artisan tinker --execute="
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Create superadmin user
\$user = User::create([
    'name' => 'Super Admin',
    'email' => 'superadmin@test.com',
    'password' => bcrypt('password123'),
    'email_verified_at' => now(),
]);

// Create superadmin role if it doesn't exist
\$role = Role::firstOrCreate(['name' => 'superadmin']);

// Assign all permissions to superadmin role
\$permissions = Permission::all();
\$role->syncPermissions(\$permissions);

// Assign superadmin role to user
\$user->assignRole('superadmin');

echo 'Superadmin user created successfully!';
echo 'Email: superadmin@test.com';
echo 'Password: password123';
"

echo.
echo Step 2: Creating additional test users...
php artisan tinker --execute="
use App\Models\User;
use Spatie\Permission\Models\Role;

// Create admin user
\$admin = User::create([
    'name' => 'Admin User',
    'email' => 'admin@test.com',
    'password' => bcrypt('admin123'),
    'email_verified_at' => now(),
]);

// Create admin role if it doesn't exist
\$adminRole = Role::firstOrCreate(['name' => 'admin']);

// Assign admin role to user
\$admin->assignRole('admin');

echo 'Admin user created successfully!';
echo 'Email: admin@test.com';
echo 'Password: admin123';
"

echo.
echo Step 3: Creating regular user...
php artisan tinker --execute="
use App\Models\User;
use Spatie\Permission\Models\Role;

// Create regular user
\$user = User::create([
    'name' => 'Test User',
    'email' => 'user@test.com',
    'password' => bcrypt('user123'),
    'email_verified_at' => now(),
]);

// Create user role if it doesn't exist
\$userRole = Role::firstOrCreate(['name' => 'user']);

// Assign user role
\$user->assignRole('user');

echo 'Regular user created successfully!';
echo 'Email: user@test.com';
echo 'Password: user123';
"

echo.
echo Step 4: Creating permissions...
php artisan tinker --execute="
use Spatie\Permission\Models\Permission;

// Create common permissions
\$permissions = [
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

foreach (\$permissions as \$permission) {
    Permission::firstOrCreate(['name' => \$permission]);
}

echo 'Permissions created successfully!';
"

echo.
echo Step 5: Assigning permissions to roles...
php artisan tinker --execute="
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Get roles
\$superadminRole = Role::where('name', 'superadmin')->first();
\$adminRole = Role::where('name', 'admin')->first();
\$userRole = Role::where('name', 'user')->first();

// Get permissions
\$allPermissions = Permission::all();
\$adminPermissions = Permission::whereIn('name', [
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

\$userPermissions = Permission::whereIn('name', [
    'view-dashboard',
    'view-payments',
    'view-notes',
    'view-tickets'
])->get();

// Assign permissions to superadmin (all permissions)
if (\$superadminRole) {
    \$superadminRole->syncPermissions(\$allPermissions);
}

// Assign permissions to admin
if (\$adminRole) {
    \$adminRole->syncPermissions(\$adminPermissions);
}

// Assign permissions to user
if (\$userRole) {
    \$userRole->syncPermissions(\$userPermissions);
}

echo 'Permissions assigned to roles successfully!';
"

echo.
echo ========================================
echo Superadmin User Creation Completed!
echo ========================================
echo.
echo Test Users Created:
echo.
echo 1. SUPERADMIN USER:
echo    Email: superadmin@test.com
echo    Password: password123
echo    Role: superadmin (all permissions)
echo.
echo 2. ADMIN USER:
echo    Email: admin@test.com
echo    Password: admin123
echo    Role: admin (limited permissions)
echo.
echo 3. REGULAR USER:
echo    Email: user@test.com
echo    Password: user123
echo    Role: user (basic permissions)
echo.
echo You can now login with any of these accounts for testing!
echo.
echo Press any key to continue...
pause > nul
