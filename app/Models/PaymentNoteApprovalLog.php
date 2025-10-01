<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentNoteApprovalLog extends Model
{
    protected $fillable = ['payment_note_id', 'priority_id', 'reviewer_id', 'status', 'comments'];

    public function paymentNoteApprovalPriority()
    {
        return $this->belongsTo(PaymentNoteApprovalPriority::class, 'priority_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
    public function priorities()
    {
        return $this->belongsToMany(PaymentNoteApprovalPriority::class, 'payment_note_log_priority', 'payment_note_approval_log_id', 'priority_id')->withTimestamps();
    }
    public function logPriorities()
    {
        return $this->hasMany(PaymentNoteLogPriority::class, 'payment_note_approval_log_id');
    }
    public function logs()
    {
        return $this->belongsToMany(PaymentNoteApprovalLog::class, 'payment_note_log_priority', 'priority_id', 'payment_note_approval_log_id')->withTimestamps();
    }
}
