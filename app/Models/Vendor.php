<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesSharedDatabase;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    // use HasFactory, SoftDeletes;
    use HasFactory, UsesSharedDatabase;
    
    /**
     * The connection name for the model.
     * Always use main database for vendors
     *
     * @var string
     */
    protected $connection = 'mysql';
    protected $guarded = [];
    protected $table = 'vendors';

    protected $fillable = [
        's_no', //
        'from_account_type',
        'status',
        'project',
        'account_name',
        'short_name',
        'parent',
        'account_number',
        'name_of_bank',
        'ifsc_code_id',
        'ifsc_code',
        'vendor_type',
        'vendor_code',
        'code_auto_generated',
        'vendor_name',
        'vendor_email',
        'vendor_mobile',
        'activity_type',
        'vendor_nick_name',
        'email',
        'mobile',
        'gstin',
        'pan',
        'pin',
        'country_id',
        'state_id',
        'city_id',
        'country_name',
        'state_name',
        'city_name',
        'msme_classification',
        'msme',
        'msme_registration_number',
        'msme_start_date',
        'msme_end_date',
        'material_nature',
        'gst_defaulted',
        'section_206AB_verified',
        'benificiary_name',
        'remarks_address',
        'common_bank_details',
        'income_tax_type',
        'date_added',
        'last_updated',
        'file_path',
        'active',
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'msme_start_date' => 'datetime',
            'msme_end_date' => 'datetime',
            'date_added' => 'datetime',
            'last_updated' => 'datetime',
            'code_auto_generated' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::parse($value);
    }

    public function getDateAttribute()
    {
        return $this->attributes['date'] = Carbon::parse($this->attributes['date'])->format('Y-m-d');
    }

    public function country()
    {
        return $this->hasOne(Country::class, 'country_id', 'id');
    }
    public function state()
    {
        return $this->hasOne(State::class, 'state_id', 'id');
    }
    public function city()
    {
        return $this->hasOne(City::class, 'city_id', 'id');
    }

    /**
     * Get all accounts for this vendor
     */
    public function accounts()
    {
        return $this->hasMany(VendorAccount::class);
    }

    /**
     * Get the primary account for this vendor
     */
    public function primaryAccount()
    {
        return $this->hasOne(VendorAccount::class)->where('is_primary', true);
    }

    /**
     * Get active accounts for this vendor
     */
    public function activeAccounts()
    {
        return $this->hasMany(VendorAccount::class)->where('is_active', true);
    }

    /**
     * Auto-generate vendor code if not provided
     */
    public static function generateVendorCode($vendorName, $vendorType = null)
    {
        // Extract first 3 characters from vendor name
        $namePrefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $vendorName), 0, 3));
        
        // Add vendor type prefix if available
        $typePrefix = $vendorType ? strtoupper(substr($vendorType, 0, 2)) : 'VN';
        
        // Get current year
        $year = date('y');
        
        // Find the next sequence number
        $lastVendor = static::where('vendor_code', 'like', "{$typePrefix}{$namePrefix}{$year}%")
            ->orderBy('vendor_code', 'desc')
            ->first();
            
        $sequence = 1;
        if ($lastVendor) {
            $lastSequence = (int) substr($lastVendor->vendor_code, -4);
            $sequence = $lastSequence + 1;
        }
        
        return $typePrefix . $namePrefix . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Boot method to handle vendor code auto-generation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vendor) {
            if (empty($vendor->vendor_code) && !empty($vendor->vendor_name)) {
                $vendor->vendor_code = static::generateVendorCode($vendor->vendor_name, $vendor->vendor_type);
                $vendor->code_auto_generated = true;
            }
        });
    }
}
