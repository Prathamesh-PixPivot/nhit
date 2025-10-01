<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalStep extends Model
{
    protected $fillable = ['approval_flow_id', 'step', 'next_on_approve', 'amount', 'next_on_reject'];

    public function approvalFlow()
    {
        return $this->belongsTo(ApprovalFlow::class);
    }

    public function nextOnApprove()
    {
        return $this->belongsTo(User::class, 'next_on_approve');
    }
}
