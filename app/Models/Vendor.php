<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    // use HasFactory, SoftDeletes;
    use HasFactory;
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
}
