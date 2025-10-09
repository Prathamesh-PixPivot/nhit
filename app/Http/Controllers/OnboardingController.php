<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class OnboardingController extends Controller
{
    /**
     * Show the onboarding welcome page
     */
    public function welcome()
    {
        // Check if system is already initialized
        if ($this->isSystemInitialized()) {
            return redirect()->route('backend.login');
        }

        return view('onboarding.welcome');
    }

    /**
     * Show the organization setup form
     */
    public function setupOrganization()
    {
        if ($this->isSystemInitialized()) {
            return redirect()->route('backend.login');
        }

        return view('onboarding.setup-organization');
    }

    /**
     * Show the superadmin user creation form
     */
    public function setupSuperAdmin(Request $request)
    {
        if ($this->isSystemInitialized()) {
            return redirect()->route('backend.login');
        }

        $orgData = $request->session()->get('org_data');
        if (!$orgData) {
            return redirect()->route('onboarding.setup-organization')
                ->with('error', 'Please complete organization setup first.');
        }

        return view('onboarding.setup-superadmin', compact('orgData'));
    }

    /**
     * Store organization data in session
     */
    public function storeOrganization(Request $request)
    {
        $request->validate([
            'organization_name' => 'required|string|max:255',
            'organization_code' => 'required|string|max:10|unique:organizations,code',
            'organization_description' => 'nullable|string|max:1000',
            'organization_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $logoPath = null;
        if ($request->hasFile('organization_logo')) {
            $logoPath = $request->file('organization_logo')->store('organizations', 'public');
        }

        $orgData = [
            'name' => $request->organization_name,
            'code' => strtoupper($request->organization_code),
            'description' => $request->organization_description,
            'logo' => $logoPath,
            'database_name' => Organization::generateDatabaseName($request->organization_code)
        ];

        $request->session()->put('org_data', $orgData);

        return redirect()->route('onboarding.setup-superadmin');
    }

    /**
     * Complete the onboarding process
     */
    public function complete(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'designation' => 'required|string|max:255',
            'department' => 'required|string|max:255'
        ]);

        $orgData = $request->session()->get('org_data');
        if (!$orgData) {
            return redirect()->route('onboarding.setup-organization')
                ->with('error', 'Session expired. Please start again.');
        }

        try {
            DB::beginTransaction();

            // Create default roles and permissions first
            $this->createDefaultRolesAndPermissions();

            // Create the organization
            $organization = Organization::create([
                'name' => $orgData['name'],
                'code' => $orgData['code'],
                'database_name' => $orgData['database_name'],
                'description' => $orgData['description'] ?? null,
                'logo' => $orgData['logo'] ?? null,
                'is_active' => true,
                'created_by' => 1 // Temporary, will update after user creation
            ]);

            // Create designation
            $designation = \App\Models\Designation::create([
                'name' => $request->designation,
                'description' => 'Default designation'
            ]);

            // Create department
            $department = \App\Models\Department::create([
                'name' => $request->department,
                'description' => 'Default department'
            ]);

            // Create superadmin user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'organization_id' => $organization->id,
                'current_organization_id' => $organization->id,
                'designation_id' => $designation->id,
                'department_id' => $department->id
            ]);

            // Assign superadmin role
            $superAdminRole = Role::where('name', 'superadmin')->first();
            if ($superAdminRole) {
                $user->assignRole($superAdminRole);
            }

            // Update organization created_by
            $organization->update(['created_by' => $user->id]);

            DB::commit();

            // Create database and clone structure (outside transaction)
            if ($organization->createDatabase()) {
                Log::info("Database created for organization: {$organization->name}");
                
                if ($organization->cloneDatabaseStructure()) {
                    Log::info("Database structure cloned successfully");
                } else {
                    Log::warning("Database structure cloning failed, but system is functional");
                }
            }

            // Clear session data
            $request->session()->forget('org_data');

            // Log the user in
            Auth::login($user);

            return redirect()->route('onboarding.success')
                ->with('success', 'Welcome to NHIT! Your organization has been set up successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Onboarding failed: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Setup failed: ' . $e->getMessage());
        }
    }

    /**
     * Show success page
     */
    public function success()
    {
        if (!Auth::check()) {
            return redirect()->route('backend.login');
        }

        return view('onboarding.success');
    }

    /**
     * Check if system is initialized
     */
    protected function isSystemInitialized(): bool
    {
        return Organization::exists() && User::role('superadmin')->exists();
    }

    /**
     * Create default roles and permissions
     */
    protected function createDefaultRolesAndPermissions(): void
    {
        // Check if superadmin role exists
        $superAdminRole = Role::where('name', 'superadmin')->first();
        
        if (!$superAdminRole) {
            $superAdminRole = Role::create(['name' => 'superadmin']);
            
            // Create basic permissions
            $permissions = [
                'view-user', 'create-user', 'edit-user', 'delete-user',
                'view-role', 'create-role', 'edit-role', 'delete-role',
                'view-note', 'create-note', 'edit-note', 'delete-note',
                'view-payment-note', 'create-payment-note', 'edit-payment-note', 'delete-payment-note',
                'view-reimbursement-note', 'create-reimbursement-note', 'edit-reimbursement-note', 'delete-reimbursement-note',
                'view-department', 'create-department', 'edit-department', 'delete-department',
                'view-designation', 'create-designation', 'edit-designation', 'delete-designation',
                'view-vendors', 'create-vendors', 'edit-vendors', 'delete-vendors'
            ];

            foreach ($permissions as $permissionName) {
                $permission = Permission::firstOrCreate(['name' => $permissionName]);
                $superAdminRole->givePermissionTo($permission);
            }
        }
    }
}
