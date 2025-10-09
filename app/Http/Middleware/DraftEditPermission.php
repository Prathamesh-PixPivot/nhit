<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DraftEditPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Get the model from route parameters
        $paymentNote = $request->route('paymentNote');
        $greenNote = $request->route('greenNote');

        // Check if user has draft edit permissions
        if ($this->canEditDraft($user, $paymentNote, $greenNote)) {
            return $next($request);
        }

        abort(403, 'You do not have permission to edit drafts.');
    }

    /**
     * Check if user can edit drafts
     */
    private function canEditDraft($user, $paymentNote = null, $greenNote = null)
    {
        // SuperAdmin can edit everything
        $superAdminRole = config('draft_permissions.superadmin_role', 'Super Admin');
        if ($user->hasRole($superAdminRole)) {
            return true;
        }

        // Check if user has specific draft edit permission
        if ($user->can('edit-drafts')) {
            return true;
        }

        // Check configurable draft edit roles
        $draftEditRoles = config('draft_permissions.draft_edit_roles', []);
        if ($user->hasAnyRole($draftEditRoles)) {
            return true;
        }

        // Payment Note specific checks
        if ($paymentNote) {
            // Creator can edit their own drafts (if enabled in config)
            if (config('draft_permissions.allow_creator_access', true)) {
                if ($paymentNote->created_by === $user->id || $paymentNote->user_id === $user->id) {
                    return true;
                }
            }

            // Check if it's a draft
            if (!$paymentNote->isDraft()) {
                return false;
            }

            // Department-based access for green note related payment notes (if enabled)
            if (config('draft_permissions.enable_department_access', true)) {
                if ($paymentNote->greenNote && $paymentNote->greenNote->department) {
                    // Check if user belongs to the same department
                    if ($user->department_id === $paymentNote->greenNote->department_id) {
                        return true;
                    }
                }
            }
        }

        // Green Note specific checks
        if ($greenNote) {
            // Creator can edit their own notes (if enabled in config)
            if (config('draft_permissions.allow_creator_access', true)) {
                if ($greenNote->user_id === $user->id) {
                    return true;
                }
            }

            // Department-based access (if enabled)
            if (config('draft_permissions.enable_department_access', true)) {
                if ($user->department_id === $greenNote->department_id) {
                    return true;
                }
            }
        }

        return false;
    }
}
