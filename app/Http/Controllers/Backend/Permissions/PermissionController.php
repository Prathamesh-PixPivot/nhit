<?php

namespace App\Http\Controllers\Backend\Permissions;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class PermissionController extends Controller
{
    /**
     * Display the permissions management interface
     */
    public function index()
    {
        // Only SuperAdmin can access this
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }

        $currentPermissions = config('draft_permissions');

        // Get all available roles
        $allRoles = \Spatie\Permission\Models\Role::all()->pluck('name')->toArray();

        // Get all users for role assignment
        $users = User::with('roles')->orderBy('name')->get();

        return view('backend.permissions.index', compact('currentPermissions', 'allRoles', 'users'));
    }

    /**
     * Update permissions configuration
     */
    public function update(Request $request)
    {
        // Only SuperAdmin can access this
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'draft_edit_roles' => 'required|array',
            'payment_note_permissions' => 'required|array',
            'green_note_permissions' => 'required|array',
            'vendor_permissions' => 'required|array',
            'superadmin_role' => 'required|string',
            'enable_department_access' => 'boolean',
            'allow_creator_access' => 'boolean',
        ]);

        try {
            $configPath = config_path('draft_permissions.php');

            $configContent = "<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Draft Edit Permissions Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which roles can edit drafts and perform various operations
    |
    */

    'draft_edit_roles' => " . var_export($request->draft_edit_roles, true) . ",

    'payment_note_permissions' => " . var_export($request->payment_note_permissions, true) . ",

    'green_note_permissions' => " . var_export($request->green_note_permissions, true) . ",

    'vendor_permissions' => " . var_export($request->vendor_permissions, true) . ",

    /*
    |--------------------------------------------------------------------------
    | SuperAdmin Full Access
    |--------------------------------------------------------------------------
    |
    | SuperAdmin role that has access to everything
    |
    */
    'superadmin_role' => '" . $request->superadmin_role . "',

    /*
    |--------------------------------------------------------------------------
    | Department Based Access
    |--------------------------------------------------------------------------
    |
    | Enable department-based access control
    |
    */
    'enable_department_access' => " . ($request->enable_department_access ? 'true' : 'false') . ",

    /*
    |--------------------------------------------------------------------------
    | Creator Access
    |--------------------------------------------------------------------------
    |
    | Allow creators to edit their own records
    |
    */
    'allow_creator_access' => " . ($request->allow_creator_access ? 'true' : 'false') . ",
];";

            // Write the new configuration
            File::put($configPath, $configContent);

            // Clear config cache
            Cache::forget('draft_permissions');
            \Artisan::call('config:clear');

            return redirect()
                ->route('backend.permissions.index')
                ->with('success', 'Permissions configuration updated successfully.');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to update permissions: ' . $e->getMessage()]);
        }
    }

    /**
     * Get available roles for AJAX requests
     */
    public function getRoles(Request $request)
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }

        $search = $request->get('search', '');
        $roles = \Spatie\Permission\Models\Role::where('name', 'like', "%{$search}%")
            ->pluck('name')
            ->toArray();

        return response()->json($roles);
    }

    /**
     * Test current permissions
     */
    public function test(Request $request)
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }

        $userId = $request->get('user_id');
        $action = $request->get('action');
        $model = $request->get('model');

        if (!$userId || !$action || !$model) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $permissionService = new \App\Services\PermissionService();

        switch ($model) {
            case 'payment_note':
                switch ($action) {
                    case 'view_all':
                        $result = $permissionService->canViewAllPaymentNotes($user);
                        break;
                    case 'create':
                        $result = $permissionService->canCreatePaymentNote($user);
                        break;
                    case 'edit':
                        $result = $permissionService->canEditPaymentNote($user);
                        break;
                    case 'delete':
                        $result = $permissionService->canDeletePaymentNote($user);
                        break;
                    case 'approve':
                        $result = $permissionService->canApprovePaymentNote($user);
                        break;
                    default:
                        return response()->json(['error' => 'Invalid action'], 400);
                }
                break;

            case 'green_note':
                switch ($action) {
                    case 'view_all':
                        $result = $permissionService->canViewAllGreenNotes($user);
                        break;
                    case 'create':
                        $result = $permissionService->canCreateGreenNote($user);
                        break;
                    case 'edit':
                        $result = $permissionService->canEditGreenNote($user);
                        break;
                    case 'delete':
                        $result = $permissionService->canDeleteGreenNote($user);
                        break;
                    case 'approve':
                        $result = $permissionService->canApproveGreenNote($user);
                        break;
                    case 'hold':
                        $result = $permissionService->canHoldGreenNote($user);
                        break;
                    default:
                        return response()->json(['error' => 'Invalid action'], 400);
                }
                break;

            case 'vendor':
                switch ($action) {
                    case 'view_all':
                        $result = $permissionService->canViewAllVendors($user);
                        break;
                    case 'create':
                        $result = $permissionService->canCreateVendor($user);
                        break;
                    case 'edit':
                        $result = $permissionService->canEditVendor($user);
                        break;
                    case 'delete':
                        $result = $permissionService->canDeleteVendor($user);
                        break;
                    case 'manage_accounts':
                        $result = $permissionService->canManageVendorAccounts($user);
                        break;
                    default:
                        return response()->json(['error' => 'Invalid action'], 400);
                }
                break;

            default:
                return response()->json(['error' => 'Invalid model'], 400);
        }

        return response()->json([
            'user' => $user->name,
            'role' => $user->getRoleNames()->first(),
            'action' => $action,
            'model' => $model,
            'has_permission' => $result,
        ]);
    }

    /**
     * Reset permissions to default
     */
    public function reset()
    {
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }

        try {
            // Restore default configuration
            $defaultConfig = "<?php

return [
    'draft_edit_roles' => [
        'Super Admin',
        'Project Accounts Team',
        'Accounts Team',
        'Finance Team',
    ],

    'payment_note_permissions' => [
        'view_all' => [
            'Super Admin',
            'Finance Manager',
            'Accounts Head',
        ],
        'create' => [
            'Super Admin',
            'Accounts Team',
            'Project Accounts Team',
        ],
        'edit' => [
            'Super Admin',
            'Accounts Team',
            'Project Accounts Team',
        ],
        'delete' => [
            'Super Admin',
            'Accounts Head',
        ],
        'approve' => [
            'Super Admin',
            'Finance Manager',
            'Accounts Head',
        ],
    ],

    'green_note_permissions' => [
        'view_all' => [
            'Super Admin',
            'Finance Manager',
            'Department Head',
        ],
        'create' => [
            'Super Admin',
            'Department User',
            'Project Team',
        ],
        'edit' => [
            'Super Admin',
            'Department User',
            'Project Team',
        ],
        'delete' => [
            'Super Admin',
            'Department Head',
        ],
        'approve' => [
            'Super Admin',
            'Department Head',
            'Finance Manager',
        ],
        'hold' => [
            'Super Admin',
            'Finance Manager',
            'Accounts Head',
        ],
    ],

    'vendor_permissions' => [
        'view_all' => [
            'Super Admin',
            'Vendor Manager',
            'Accounts Team',
        ],
        'create' => [
            'Super Admin',
            'Vendor Manager',
            'Accounts Team',
        ],
        'edit' => [
            'Super Admin',
            'Vendor Manager',
            'Accounts Team',
        ],
        'delete' => [
            'Super Admin',
            'Vendor Manager',
        ],
        'manage_accounts' => [
            'Super Admin',
            'Vendor Manager',
            'Accounts Team',
        ],
    ],

    'superadmin_role' => 'Super Admin',
    'enable_department_access' => true,
    'allow_creator_access' => true,
];";

            File::put(config_path('draft_permissions.php'), $defaultConfig);

            // Clear cache
            Cache::forget('draft_permissions');
            \Artisan::call('config:clear');

            return redirect()
                ->route('backend.permissions.index')
                ->with('success', 'Permissions reset to default configuration.');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to reset permissions: ' . $e->getMessage()]);
        }
    }
}
