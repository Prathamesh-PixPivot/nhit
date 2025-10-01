<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankLetterApprovalLog extends Model
{
    protected $fillable = ['payment_id', 'sl_no', 'priority_id', 'reviewer_id', 'status', 'comments'];

    public function bankLetterApprovalPriority()
    {
        return $this->belongsTo(BankLetterApprovalPriority::class, 'priority_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
    public function priorities()
    {
        return $this->belongsToMany(BankLetterApprovalPriority::class, 'bank_letter_log_priority', 'bank_letter_approval_log_id', 'priority_id')->withTimestamps();
    }
    public function logPriorities()
    {
        return $this->hasMany(BankLetterLogPriority::class, 'bank_letter_approval_log_id');
    }
    public function logs()
    {
        return $this->belongsToMany(BankLetterApprovalLog::class, 'bank_letter_log_priority', 'priority_id', 'bank_letter_approval_log_id')->withTimestamps();
    }
}
