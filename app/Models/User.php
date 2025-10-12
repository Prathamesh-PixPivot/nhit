<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\UsesSharedDatabase;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, UsesSharedDatabase;

    /**
     * The connection name for the model.
     * Always use main database for users
     *
     * @var string
     */
    protected $connection = 'mysql';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    /*  protected $fillable = [
        'name',
        'email',
        'password',
    ]; */

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];
    /**
     * The event map for the model.
     *
     * @var array<string, string>
     */
    /* protected $dispatchesEvents = [
        'updated' => \App\Events\UserUpdated::class,
    ]; */

    /**
     * The "booted" method of the model.
     */
    /* protected static function booted(): void
    {
        static::updated(function (User $user) {
            // ...
            dd("static::updated User Model");
        });
    } */

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // using seperate scope class
        // static::addGlobalScope(new HasActiveScope);
        // you can do the same thing using anonymous function
        // let's add another scope using anonymous function
        /* static::addGlobalScope('status', function (Builder $builder) {
            $builder->where('status', 'Active');
        }); */
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function userLoginHistory()
    {
        return $this->hasMany(UserLoginHistory::class);
    }
    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function approvalSteps()
    {
        return $this->belongsToMany(PaymentNoteApprovalStep::class, 'payment_note_approval_priorities', 'reviewer_id', 'approval_step_id');
    }

    /**
     * Get the organization this user belongs to
     */
    public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class);
    }

    /**
     * Get the current organization context for this user
     */
    public function currentOrganization()
    {
        if ($this->current_organization_id) {
            return \App\Models\Organization::on('mysql')->find($this->current_organization_id);
        }
        return $this->organization;
    }

    /**
     * Get all organizations this user can access
     */
    public function accessibleOrganizations()
    {
        // SuperAdmin can access all organizations (always from main database)
        if ($this->hasRole('superadmin')) {
            return \App\Models\Organization::on('mysql')->active()->get();
        }
        
        // Regular users can access organizations they belong to or created (always from main database)
        return \App\Models\Organization::on('mysql')->where(function($query) {
            $query->where('created_by', $this->id)
                  ->orWhereHas('users', function($q) {
                      $q->where('users.id', $this->id);
                  });
        })->active()->get();
    }

    /**
     * Switch to a different organization context
     */
    public function switchToOrganization(int $organizationId): bool
    {
        \Log::info("User {$this->id} attempting to switch to organization {$organizationId}");
        
        // Always use mysql connection for organization queries
        $organization = \App\Models\Organization::on('mysql')->find($organizationId);
        
        if (!$organization || !$organization->is_active) {
            \Log::error("Organization {$organizationId} not found or inactive");
            return false;
        }
        
        // Check if user has access to this organization
        if (!$this->hasRole('superadmin') && $organization->created_by !== $this->id) {
            \Log::error("User {$this->id} does not have access to organization {$organizationId}");
            return false;
        }
        
        \Log::info("Updating user {$this->id} current_organization_id to {$organizationId}");
        
        // Use setConnection to ensure we're updating on the correct database
        $this->setConnection('mysql');
        $this->current_organization_id = $organizationId;
        $this->save();
        
        // Check if user already exists in target organization (use cache)
        $cacheKey = "user_migrated_{$this->id}_org_{$organizationId}";
        $isMigrated = \Cache::get($cacheKey, false);
        
        \Log::info("Migration cache status for user {$this->id} to org {$organizationId}: " . ($isMigrated ? 'cached' : 'needs migration'));
        
        if (!$isMigrated) {
            try {
                \Log::info("Starting migration for user {$this->id} to org {$organizationId}");
                
                // Migrate user to new organization (optimized for speed)
                $this->migrateToOrganizationFast($organization);
                
                // Cache the migration status for 24 hours
                \Cache::put($cacheKey, true, now()->addHours(24));
                \Log::info("Migration completed and cached for user {$this->id} to org {$organizationId}");
            } catch (\Exception $e) {
                \Log::error("Migration failed for user {$this->id} to org {$organizationId}: " . $e->getMessage());
                \Log::error("Migration error trace: " . $e->getTraceAsString());
                
                // Still return true to allow the switch, but log the error
                // The user will be created on-demand when they access organization-specific features
                \Log::warning("Continuing with switch despite migration failure - user will be created on-demand");
            }
        }
        
        \Log::info("Organization switch completed successfully for user {$this->id}");
        return true;
    }

    /**
     * Fast migration method - optimized for speed
     * Updated for dual-database architecture
     */
    protected function migrateToOrganizationFast(Organization $organization): void
    {
        $mainDatabase = config('database.connections.mysql.database');
        
        try {
            // Skip if same database as main
            if ($organization->database_name === $mainDatabase) {
                \Log::info("Skipping migration - organization uses main database: {$organization->database_name}");
                return;
            }
            
            // Configure organization connection
            \Log::info("Configuring organization connection to: {$organization->database_name}");
            config(['database.connections.organization.database' => $organization->database_name]);
            DB::purge('organization');
            DB::reconnect('organization');
            
            // Test the connection
            try {
                DB::connection('organization')->getPdo();
                \Log::info("Organization database connection successful");
            } catch (\Exception $e) {
                \Log::error("Failed to connect to organization database: " . $e->getMessage());
                throw new \Exception("Cannot connect to organization database: {$organization->database_name}");
            }
            
            // Quick existence check in organization database
            \Log::info("Checking if user exists in organization database");
            $exists = DB::connection('organization')->table('users')->where('email', $this->email)->exists();
            \Log::info("User exists in org database: " . ($exists ? 'yes' : 'no'));
            
            if (!$exists) {
                // Insert user data into organization database
                $newUserId = DB::connection('organization')->table('users')->insertGetId([
                    'name' => $this->name,
                    'username' => $this->username,
                    'email' => $this->email,
                    'password' => $this->password,
                    'organization_id' => $organization->id,
                    'designation_id' => $this->designation_id,
                    'department_id' => $this->department_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // Get roles from main database (mysql connection stays on main DB)
                $roles = DB::connection('mysql')->table('model_has_roles')
                    ->where('model_type', User::class)
                    ->where('model_id', $this->id)
                    ->pluck('role_id');
                
                // Insert roles into organization database
                if ($roles->isNotEmpty()) {
                    $roleInserts = $roles->map(fn($roleId) => [
                        'role_id' => $roleId,
                        'model_type' => User::class,
                        'model_id' => $newUserId
                    ])->toArray();
                    
                    DB::connection('organization')->table('model_has_roles')->insert($roleInserts);
                }
            }
            
        } catch (\Exception $e) {
            \Log::error("Fast migration failed for user {$this->email} to organization {$organization->name}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Migrate user and roles to target organization
     */
    protected function migrateToOrganization(Organization $organization): void
    {
        try {
            $currentDatabase = config('database.connections.mysql.database');
            
            // Quick check: if it's the same database, no migration needed
            if ($organization->database_name === $currentDatabase) {
                return;
            }
            
            // Switch to organization database
            config(['database.connections.mysql.database' => $organization->database_name]);
            \DB::purge('mysql');
            \DB::reconnect('mysql');
            
            // Quick check if user exists
            $existingUser = \DB::table('users')->where('email', $this->email)->first();
            
            if (!$existingUser) {
                // Create user in target organization (optimized)
                $userData = [
                    'name' => $this->name,
                    'username' => $this->username,
                    'email' => $this->email,
                    'password' => $this->password,
                    'organization_id' => $organization->id,
                    'current_organization_id' => $organization->id,
                    'designation_id' => $this->designation_id,
                    'department_id' => $this->department_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                $newUserId = \DB::table('users')->insertGetId($userData);
                
                // Migrate roles and permissions (async for speed)
                $this->migrateUserRolesOptimized($newUserId, $currentDatabase, $organization->database_name);
            }
            
            // Switch back to main database
            config(['database.connections.mysql.database' => $currentDatabase]);
            \DB::purge('mysql');
            \DB::reconnect('mysql');
            
        } catch (\Exception $e) {
            \Log::error("Failed to migrate user {$this->email} to organization {$organization->name}: " . $e->getMessage());
            
            // Ensure we switch back to main database on error
            $currentDatabase = config('database.connections.mysql.database');
            config(['database.connections.mysql.database' => $currentDatabase]);
            \DB::purge('mysql');
            \DB::reconnect('mysql');
        }
    }

    /**
     * Optimized role migration method
     */
    protected function migrateUserRolesOptimized(int $targetUserId, string $mainDatabase, string $orgDatabase): void
    {
        try {
            // Get roles and permissions from main database using raw query for speed
            config(['database.connections.mysql.database' => $mainDatabase]);
            \DB::purge('mysql');
            \DB::reconnect('mysql');
            
            $userRoles = \DB::table('model_has_roles')
                ->where('model_type', User::class)
                ->where('model_id', $this->id)
                ->pluck('role_id')
                ->toArray();
            
            $userPermissions = \DB::table('model_has_permissions')
                ->where('model_type', User::class)
                ->where('model_id', $this->id)
                ->pluck('permission_id')
                ->toArray();
            
            // Switch to organization database
            config(['database.connections.mysql.database' => $orgDatabase]);
            \DB::purge('mysql');
            \DB::reconnect('mysql');
            
            // Bulk insert roles (faster than individual inserts)
            if (!empty($userRoles)) {
                $roleData = array_map(function($roleId) use ($targetUserId) {
                    return [
                        'role_id' => $roleId,
                        'model_type' => User::class,
                        'model_id' => $targetUserId
                    ];
                }, $userRoles);
                
                \DB::table('model_has_roles')->insert($roleData);
            }
            
            // Bulk insert permissions (faster than individual inserts)
            if (!empty($userPermissions)) {
                $permissionData = array_map(function($permissionId) use ($targetUserId) {
                    return [
                        'permission_id' => $permissionId,
                        'model_type' => User::class,
                        'model_id' => $targetUserId
                    ];
                }, $userPermissions);
                
                \DB::table('model_has_permissions')->insert($permissionData);
            }
            
            // Switch back to main database
            config(['database.connections.mysql.database' => $mainDatabase]);
            \DB::purge('mysql');
            \DB::reconnect('mysql');
            
        } catch (\Exception $e) {
            \Log::error("Failed to migrate roles for user {$this->email}: " . $e->getMessage());
        }
    }

    /**
     * Migrate user roles to target organization (legacy method)
     */
    protected function migrateUserRoles(int $targetUserId): void
    {
        try {
            // Get current user roles from main database
            $currentDatabase = config('database.connections.mysql.database');
            
            // Switch back to main database to get roles
            config(['database.connections.mysql.database' => $currentDatabase]);
            \DB::purge('mysql');
            \DB::reconnect('mysql');
            
            $userRoles = \DB::table('model_has_roles')
                ->where('model_type', User::class)
                ->where('model_id', $this->id)
                ->get();
            
            $userPermissions = \DB::table('model_has_permissions')
                ->where('model_type', User::class)
                ->where('model_id', $this->id)
                ->get();
            
            // Switch back to organization database
            $orgDatabase = $this->currentOrganization()->database_name;
            config(['database.connections.mysql.database' => $orgDatabase]);
            \DB::purge('mysql');
            \DB::reconnect('mysql');
            
            // Clear existing roles and permissions
            \DB::table('model_has_roles')
                ->where('model_type', User::class)
                ->where('model_id', $targetUserId)
                ->delete();
                
            \DB::table('model_has_permissions')
                ->where('model_type', User::class)
                ->where('model_id', $targetUserId)
                ->delete();
            
            // Insert roles
            foreach ($userRoles as $role) {
                \DB::table('model_has_roles')->insert([
                    'role_id' => $role->role_id,
                    'model_type' => User::class,
                    'model_id' => $targetUserId
                ]);
            }
            
            // Insert permissions
            foreach ($userPermissions as $permission) {
                \DB::table('model_has_permissions')->insert([
                    'permission_id' => $permission->permission_id,
                    'model_type' => User::class,
                    'model_id' => $targetUserId
                ]);
            }
            
            // Switch back to main database
            config(['database.connections.mysql.database' => $currentDatabase]);
            \DB::purge('mysql');
            \DB::reconnect('mysql');
            
        } catch (\Exception $e) {
            \Log::error("Failed to migrate roles for user {$this->email}: " . $e->getMessage());
        }
    }
}
