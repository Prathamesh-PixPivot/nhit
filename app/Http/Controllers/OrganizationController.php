<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Jobs\MigrateUserToOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:superadmin')->except(['switch', 'getCurrentOrganizations']);
    }

    /**
     * Display a listing of organizations
     */
    public function index()
    {
        $organizations = Organization::accessibleBy(Auth::user())
            ->with('creator')
            ->latest()
            ->paginate(10);

        return view('backend.organizations.index', compact('organizations'));
    }

    /**
     * Show the form for creating a new organization
     */
    public function create()
    {
        return view('backend.organizations.create');
    }

    /**
     * Store a newly created organization
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:organizations,code',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('organizations', 'public');
            }

            $databaseName = Organization::generateDatabaseName($request->code);

            $organization = Organization::create([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'database_name' => $databaseName,
                'description' => $request->description,
                'logo' => $logoPath,
                'created_by' => Auth::id(),
                'is_active' => true
            ]);

            DB::commit();

            // Create database for the organization (outside transaction)
            if (!$organization->createDatabase()) {
                Log::error('Failed to create database for organization: ' . $organization->name);
                return back()->withInput()
                    ->with('error', 'Organization created but failed to create database. Please contact administrator.');
            }

            // Clone database structure (this may take time)
            Log::info("Starting database clone for organization: {$organization->name}");
            if (!$organization->cloneDatabaseStructure()) {
                Log::error('Failed to clone database structure for organization: ' . $organization->name);
                return redirect()->route('backend.organizations.index')
                    ->with('warning', 'Organization created but database structure cloning failed. Please run: php artisan nhit:clone-org-database ' . $organization->id);
            }
            
            Log::info("Database clone completed for organization: {$organization->name}");

            return redirect()->route('backend.organizations.index')
                ->with('success', 'Organization created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create organization: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Failed to create organization: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified organization
     */
    public function show(Organization $organization)
    {
        $this->authorize('view', $organization);
        
        $organization->load('creator', 'users');
        
        return view('backend.organizations.show', compact('organization'));
    }

    /**
     * Show the form for editing the specified organization
     */
    public function edit(Organization $organization)
    {
        $this->authorize('update', $organization);
        
        return view('backend.organizations.edit', compact('organization'));
    }

    /**
     * Update the specified organization
     */
    public function update(Request $request, Organization $organization)
    {
        $this->authorize('update', $organization);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:organizations,code,' . $organization->id,
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);

        try {
            $logoPath = $organization->logo;
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($logoPath) {
                    \Storage::disk('public')->delete($logoPath);
                }
                $logoPath = $request->file('logo')->store('organizations', 'public');
            }

            $organization->update([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'logo' => $logoPath,
                'is_active' => $request->boolean('is_active', true)
            ]);

            return redirect()->route('backend.organizations.index')
                ->with('success', 'Organization updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update organization: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Failed to update organization: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified organization
     */
    public function destroy(Organization $organization)
    {
        $this->authorize('delete', $organization);

        try {
            DB::beginTransaction();

            // Delete organization database (optional - be careful!)
            // DB::statement("DROP DATABASE IF EXISTS `{$organization->database_name}`");

            // Delete logo if exists
            if ($organization->logo) {
                \Storage::disk('public')->delete($organization->logo);
            }

            $organization->delete();

            DB::commit();

            return redirect()->route('backend.organizations.index')
                ->with('success', 'Organization deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete organization: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to delete organization: ' . $e->getMessage());
        }
    }

    /**
     * Switch to a different organization context
     */
    public function switch(Request $request)
    {
        try {
            $request->validate([
                'organization_id' => 'required|integer'
            ]);

            $user = Auth::user();
            $organizationId = $request->organization_id;
            
            Log::info("Switch request: User {$user->id} to organization {$organizationId}");
            
            // Always query organizations from main database
            $organization = Organization::on('mysql')->find($organizationId);
            
            if (!$organization) {
                Log::error("Organization {$organizationId} not found");
                return response()->json([
                    'success' => false,
                    'message' => 'Organization not found.'
                ], 404);
            }

            // Check if already migrated (cached)
            $cacheKey = "user_migrated_{$user->id}_org_{$organizationId}";
            $isMigrated = \Cache::get($cacheKey, false);

            // Set a reasonable timeout for the switch operation
            set_time_limit(30); // 30 seconds max
            
            // Store organization data before switch (to avoid querying after context change)
            $orgData = [
                'id' => $organization->id,
                'name' => $organization->name,
                'code' => $organization->code
            ];
            
            if ($user->switchToOrganization($organizationId)) {
                try {
                    // Clear organization-specific dashboard cache
                    $this->clearDashboardCache($user->id, $organizationId);
                } catch (\Exception $e) {
                    // Log cache clear error but don't fail the switch
                    Log::warning("Failed to clear dashboard cache after switch: " . $e->getMessage());
                }
                
                Log::info("Switch successful: User {$user->id} to organization {$organizationId}");
                
                // Return response immediately to avoid any database context issues
                return response()->json([
                    'success' => true,
                    'message' => 'Organization switched successfully!',
                    'is_first_time' => !$isMigrated,
                    'organization' => $orgData
                ]);
            }

            Log::error("Switch failed: User {$user->id} to organization {$organizationId}");
            return response()->json([
                'success' => false,
                'message' => 'Failed to switch organization. You may not have access to this organization.'
            ], 403);
            
        } catch (\Exception $e) {
            Log::error("Switch exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while switching organization.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get organizations accessible by current user
     */
    public function getCurrentOrganizations()
    {
        $user = Auth::user();
        $organizations = $user->accessibleOrganizations();
        $currentOrg = $user->currentOrganization();

        return response()->json([
            'organizations' => $organizations,
            'current_organization' => $currentOrg
        ]);
    }

    /**
     * Toggle organization status
     */
    public function toggleStatus(Organization $organization)
    {
        $this->authorize('update', $organization);

        $organization->update([
            'is_active' => !$organization->is_active
        ]);

        $status = $organization->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Organization {$status} successfully!");
    }

    /**
     * Clear organization-specific dashboard cache
     */
    private function clearDashboardCache($userId, $orgId)
    {
        $currentMonth = now()->format('Y-m');
        
        // Clear admin cache keys
        \Cache::forget("dash_admin_org_{$orgId}_gn_all");
        \Cache::forget("dash_admin_org_{$orgId}_gn_month_{$currentMonth}");
        \Cache::forget("dash_admin_org_{$orgId}_reim_all");
        \Cache::forget("dash_admin_org_{$orgId}_reim_month_{$currentMonth}");
        \Cache::forget("dash_admin_org_{$orgId}_pay_counts_month_{$currentMonth}");
        \Cache::forget("dash_admin_org_{$orgId}_pay_counts_all");
        \Cache::forget("dash_admin_org_{$orgId}_bank_counts_{$currentMonth}");
        
        // Clear user-specific cache keys
        \Cache::forget("dash_user_{$userId}_org_{$orgId}_gn_all");
        \Cache::forget("dash_user_{$userId}_org_{$orgId}_gn_month_{$currentMonth}");
        \Cache::forget("dash_user_{$userId}_org_{$orgId}_reim_all");
        \Cache::forget("dash_user_{$userId}_org_{$orgId}_reim_month_{$currentMonth}");
        \Cache::forget("dash_user_{$userId}_org_{$orgId}_pay_counts_month_{$currentMonth}");
        \Cache::forget("dash_user_{$userId}_org_{$orgId}_pay_counts_all");
        \Cache::forget("dash_user_{$userId}_org_{$orgId}_bank_counts_{$currentMonth}");
        
        // Clear user approval data cache
        \Cache::forget("user-approval-data-org_{$orgId}");
        
        Log::info("Cleared dashboard cache for user {$userId} and organization {$orgId}");
    }
}
