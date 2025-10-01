<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PaymentNoteLogPriority extends Pivot
{
    use HasFactory;

    protected $table = 'payment_note_log_priority';

    protected $fillable = ['payment_note_approval_log_id', 'priority_id'];
    public function logs()
    {
        return $this->belongsToMany(PaymentNoteApprovalLog::class, 'payment_note_log_priority', 'priority_id', 'payment_note_approval_log_id')->withTimestamps();
    }
    public function priorities()
    {
        return $this->belongsToMany(PaymentNoteApprovalPriority::class, 'payment_note_log_priority', 'payment_note_approval_log_id', 'priority_id')->withTimestamps();
    }
    public function priority()
    {
        return $this->belongsTo(PaymentNoteApprovalPriority::class, 'priority_id');
    }
}
