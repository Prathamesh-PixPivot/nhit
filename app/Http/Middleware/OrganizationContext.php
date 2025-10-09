<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class OrganizationContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            try {
                // Share accessible organizations for all authenticated users
                $accessibleOrganizations = $user->accessibleOrganizations();
                $currentOrg = $user->currentOrganization();
                
                \Log::info("OrganizationContext: User {$user->id}, Current Org: " . ($currentOrg ? $currentOrg->name : 'None'));
                
                // Share organization data with views
                view()->share('currentOrganization', $currentOrg);
                view()->share('accessibleOrganizations', $accessibleOrganizations);
                
                // Switch database context if current organization exists
                if ($currentOrg && $currentOrg->is_active) {
                    $mainDatabase = config('database.connections.mysql.database');
                    if ($currentOrg->database_name === $mainDatabase) {
                        \Log::info("Organization uses main database - no switching needed: {$currentOrg->database_name}");
                        // Set organization connection to main database
                        config(['database.connections.organization.database' => $mainDatabase]);
                    } else {
                        \Log::info("Switching to database: {$currentOrg->database_name}");
                        $this->switchDatabaseContext($currentOrg->database_name);
                    }
                }
            } catch (\Exception $e) {
                \Log::error("OrganizationContext middleware error: " . $e->getMessage());
                // Continue without organization context
            }
        }

        return $next($request);
    }

    /**
     * Switch database context to organization database
     * NOTE: We switch the 'organization' connection, NOT 'mysql'
     * The 'mysql' connection always stays on the main database for shared tables
     */
    protected function switchDatabaseContext(string $databaseName): void
    {
        try {
            // Update the organization connection (NOT mysql - that stays on main DB)
            Config::set('database.connections.organization.database', $databaseName);
            
            // Purge and reconnect the organization connection
            DB::purge('organization');
            DB::reconnect('organization');
            
            // Don't change the default connection - let models specify their own connections
            // Config::set('database.default', 'organization');
            
        } catch (\Exception $e) {
            \Log::error("Failed to switch organization database context to {$databaseName}: " . $e->getMessage());
            
            // Fallback to main database
            $mainDatabase = config('database.connections.mysql.database');
            Config::set('database.connections.organization.database', $mainDatabase);
            DB::purge('organization');
            DB::reconnect('organization');
        }
    }
}
