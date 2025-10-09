<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UsesOrganizationDatabase;

class PaymentNoteApprovalPriority extends Model
{
    use HasFactory, UsesOrganizationDatabase;
    
    /**
     * The connection name for the model.
     * Uses organization database
     *
     * @var string
     */
    protected $connection = 'organization';

    protected $fillable = ['approval_step_id', 'reviewer_id', 'approver_level'];

    public function approvalStep()
    {
        return $this->belongsTo(PaymentNoteApprovalStep::class, 'approval_step_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
    public function logs()
    {
        return $this->belongsToMany(PaymentNoteApprovalLog::class, 'payment_note_log_priority', 'priority_id', 'payment_note_approval_log_id')->withTimestamps();
    }
}
