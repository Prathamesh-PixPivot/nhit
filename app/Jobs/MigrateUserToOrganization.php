<?php

namespace App\Jobs;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateUserToOrganization implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $organizationId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, int $organizationId)
    {
        $this->userId = $userId;
        $this->organizationId = $organizationId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $user = User::find($this->userId);
            $organization = Organization::find($this->organizationId);
            
            if (!$user || !$organization) {
                Log::error("User or organization not found for migration");
                return;
            }
            
            $currentDatabase = config('database.connections.mysql.database');
            
            // Quick check: if it's the same database, no migration needed
            if ($organization->database_name === $currentDatabase) {
                return;
            }
            
            // Switch to organization database
            config(['database.connections.mysql.database' => $organization->database_name]);
            DB::purge('mysql');
            DB::reconnect('mysql');
            
            // Check if user exists
            $existingUser = DB::table('users')->where('email', $user->email)->first();
            
            if (!$existingUser) {
                // Create user in target organization
                $userData = [
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'password' => $user->password,
                    'organization_id' => $organization->id,
                    'current_organization_id' => $organization->id,
                    'designation_id' => $user->designation_id,
                    'department_id' => $user->department_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                $newUserId = DB::table('users')->insertGetId($userData);
                
                // Migrate roles and permissions
                $this->migrateRoles($user, $newUserId, $currentDatabase, $organization->database_name);
            }
            
            // Switch back to main database
            config(['database.connections.mysql.database' => $currentDatabase]);
            DB::purge('mysql');
            DB::reconnect('mysql');
            
            // Cache the migration status
            \Cache::put("user_migrated_{$this->userId}_org_{$this->organizationId}", true, now()->addHours(24));
            
        } catch (\Exception $e) {
            Log::error("Job failed to migrate user {$this->userId} to organization {$this->organizationId}: " . $e->getMessage());
        }
    }

    /**
     * Migrate user roles
     */
    protected function migrateRoles(User $user, int $targetUserId, string $mainDatabase, string $orgDatabase): void
    {
        try {
            // Get roles from main database
            config(['database.connections.mysql.database' => $mainDatabase]);
            DB::purge('mysql');
            DB::reconnect('mysql');
            
            $userRoles = DB::table('model_has_roles')
                ->where('model_type', User::class)
                ->where('model_id', $user->id)
                ->pluck('role_id')
                ->toArray();
            
            // Switch to org database and insert roles
            config(['database.connections.mysql.database' => $orgDatabase]);
            DB::purge('mysql');
            DB::reconnect('mysql');
            
            if (!empty($userRoles)) {
                $roleData = array_map(function($roleId) use ($targetUserId) {
                    return [
                        'role_id' => $roleId,
                        'model_type' => User::class,
                        'model_id' => $targetUserId
                    ];
                }, $userRoles);
                
                DB::table('model_has_roles')->insert($roleData);
            }
            
            // Switch back
            config(['database.connections.mysql.database' => $mainDatabase]);
            DB::purge('mysql');
            DB::reconnect('mysql');
            
        } catch (\Exception $e) {
            Log::error("Failed to migrate roles: " . $e->getMessage());
        }
    }
}
