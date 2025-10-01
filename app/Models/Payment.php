<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    protected $table = 'payments_new';

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

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::parse($value);
    }
    public function getDateAttribute()
    {
        return $this->attributes['date'] = Carbon::parse($this->attributes['date'])->format('Y-m-d');
    }
    public function shortcut()
    {
        return $this->hasOne(PaymentsShortcut::class, 'sl_no', 'sl_no');
    }
    public function bankLetterApprovalLogs()
    {
        return $this->hasMany(BankLetterApprovalLog::class, 'payment_id');
    }
    public function paymentNote()
    {
        return $this->belongsTo(PaymentNote::class, 'payment_note_id');
    }
    public function getApprovalLog()
    {
        return $this->hasOne(BankLetterApprovalLog::class, 'sl_no', 'sl_no');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function approvalLogs()
    {
        return $this->hasMany(BankLetterApprovalLog::class, 'sl_no', 'sl_no');
    }
}
