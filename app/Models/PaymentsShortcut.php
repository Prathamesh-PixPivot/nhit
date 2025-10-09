<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesOrganizationDatabase;

class PaymentsShortcut extends Model
{
    use HasFactory, SoftDeletes, UsesOrganizationDatabase;
    
    /**
     * The connection name for the model.
     * Uses organization database
     *
     * @var string
     */
    protected $connection = 'organization';
    
    // protected $table = 'payments_shortcut';
    protected $guarded = [];
    // protected $table = 'payments_shortcuts';
    protected $table = 'payments_shortcuts_new';

    // Allow mass assignment for these fields
    protected $fillable = ['sl_no', 'shortcut_name', 'request_data', 'payment_id'];

    // Specify that `request_data` is a JSON column
    protected $casts = [
        'request_data' => 'array',
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }

    public function setRequestDataAttribute($value)
    {
        $this->attributes['request_data'] = json_encode($value);
    }
    public function getRequestDataAttribute()
    {
        return $this->attributes['request_data'] = !empty($this->attributes['request_data']) ? json_decode($this->attributes['request_data'], true) : null;
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'id');
    }
}
