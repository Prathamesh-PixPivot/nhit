<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserLoginHistory extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    // protected $quarded = ['id'];
    public $timestamps = false;
    // protected $fillable = ['user_id', 'date_time', 'ip'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function scopeHistory()
    {
        //return $this->hasMany('App\UserLoginHistory');
        return $this->hasMany(UserLoginHistory::class, 'user_id', 'id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // 'date' => 'datetime'
        ];
    }

    /* public function setDateAttribute($value)
    {
        $this->attributes['date_time'] = Carbon::parse($value);
    } */
}
