<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'account_name',
        'account_number',
        'account_type',
        'name_of_bank',
        'branch_name',
        'ifsc_code',
        'swift_code',
        'is_primary',
        'is_active',
        'remarks',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the vendor that owns this account
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Scope to get only active accounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get primary account
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Boot method to handle primary account logic
     */
    protected static function boot()
    {
        parent::boot();

        // When creating a new account, if it's marked as primary,
        // unset other primary accounts for the same vendor
        static::creating(function ($account) {
            if ($account->is_primary) {
                static::where('vendor_id', $account->vendor_id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }
        });

        // When updating an account to primary,
        // unset other primary accounts for the same vendor
        static::updating(function ($account) {
            if ($account->is_primary && $account->isDirty('is_primary')) {
                static::where('vendor_id', $account->vendor_id)
                    ->where('id', '!=', $account->id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }
        });
    }
}
