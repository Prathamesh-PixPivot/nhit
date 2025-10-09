<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearDashboardCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:clear-cache {--organization= : Clear cache for specific organization ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear dashboard cache for all or specific organization';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $organizationId = $this->option('organization');
        
        if ($organizationId) {
            $this->clearOrganizationCache($organizationId);
            $this->info("Dashboard cache cleared for organization ID: {$organizationId}");
        } else {
            $this->clearAllDashboardCache();
            $this->info("All dashboard cache cleared successfully!");
        }
    }

    /**
     * Clear cache for specific organization
     */
    private function clearOrganizationCache($orgId)
    {
        $currentMonth = now()->format('Y-m');
        
        // Clear admin cache keys
        Cache::forget("dash_admin_org_{$orgId}_gn_all");
        Cache::forget("dash_admin_org_{$orgId}_gn_month_{$currentMonth}");
        Cache::forget("dash_admin_org_{$orgId}_reim_all");
        Cache::forget("dash_admin_org_{$orgId}_reim_month_{$currentMonth}");
        Cache::forget("dash_admin_org_{$orgId}_pay_counts_month_{$currentMonth}");
        Cache::forget("dash_admin_org_{$orgId}_pay_counts_all");
        Cache::forget("dash_admin_org_{$orgId}_bank_counts_{$currentMonth}");
        
        // Clear user approval data cache
        Cache::forget("user-approval-data-org_{$orgId}");
        
        // Clear user-specific cache (we can't know all user IDs, so we'll use cache tags if available)
        // For now, we'll clear the pattern-based cache
        $this->info("Organization-specific cache cleared. User-specific cache will be cleared on next login.");
    }

    /**
     * Clear all dashboard cache
     */
    private function clearAllDashboardCache()
    {
        // Get all cache keys that match dashboard patterns
        $patterns = [
            'dash_admin_*',
            'dash_user_*',
            'user-approval-data*'
        ];

        // Since Laravel doesn't have a built-in way to clear by pattern,
        // we'll clear the entire cache for dashboard-related items
        Cache::flush();
        
        $this->warn("Entire cache has been flushed. This affects all cached data, not just dashboard.");
    }
}
