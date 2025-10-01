<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalLog extends Model
{
    protected $fillable = ['approval_flow_id', 'approval_step_id', 'green_note_id', 'reviewer_id', 'status', 'comments'];

    public function approvalStep()
    {
        return $this->belongsTo(ApprovalStep::class, 'approval_step_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
