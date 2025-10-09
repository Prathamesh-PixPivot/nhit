<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSystemInitialized
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if system is initialized (has organizations and superadmin)
        $isInitialized = Organization::exists() && User::role('superadmin')->exists();
        
        // If not initialized and not on onboarding routes, redirect to onboarding
        if (!$isInitialized && !$request->is('onboarding/*') && !$request->is('onboarding')) {
            return redirect()->route('onboarding.welcome');
        }
        
        // If initialized and trying to access onboarding, redirect to login
        if ($isInitialized && ($request->is('onboarding/*') || $request->is('onboarding'))) {
            return redirect()->route('backend.login');
        }
        
        return $next($request);
    }
}
